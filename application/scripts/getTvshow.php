<?php
/**
 * Script for creating and loading database
 */

// Initialize the application path and autoloading
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', dirname(__FILE__) . '/../');
set_include_path(implode(PATH_SEPARATOR, array(
	APPLICATION_PATH . '../library',
	get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
	'name|n=n' 	=> 'name of show',
	'id|i=i' 	=> 'id of show',
	'env|e-s'    => 'Application environment for which to create database (defaults to development)',
	'help|h'     => 'Help -- usage message',
));
try {
	$getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
	// Bad options passed: report usage
	echo $e->getUsageMessage();
	return false;
}

// If help requested, report usage message
if ($getopt->getOption('h')) {
	echo $getopt->getUsageMessage();
	return true;
}

// Initialize values based on presence or absence of CLI options
$name 	= $getopt->getOption('n');
$id 	= $getopt->getOption('i');
$env	= $getopt->getOption('e');
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// Initialize Zend_Application
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . 'configs/application.ini'
);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');

function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
	// error was suppressed with the @-operator
	if (0 === error_reporting()) {
		return false;
	}

	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handleError');

// this block executes the actual retrieval of anime infor
try {
	require 'phptvdb/TVDB.php';
	$tvdb = new TV_Shows();

	if ($name) {
		$request = $tvdb->search($name);
		foreach($request as $r) {
			echo $r->id . ' - ' . $r->seriesName . PHP_EOL;
		}
	} else if ($id) {
		$tvshow = $tvdb->findById($id);
		if (!$tvshow) {
			echo 'TV show not found' . PHP_EOL;
			exit(1);
		}
		$episodes = $tvshow->getSeasons();


		$data = array(
			'id'				=> $tvshow->id,
			'name'				=> $tvshow->seriesName,
			'status'			=> $tvshow->status,
			'first_aired'		=> date('Y-m-d', $tvshow->firstAired),
			'network'			=> $tvshow->network,
			'runtime'			=> $tvshow->runtime,
			'overview'			=> $tvshow->overview,
			'day_of_week' 		=> $tvshow->dayOfWeek,
			'air_time' 			=> $tvshow->airTime,
			'imdb_id' 			=> $tvshow->imdbId
		);

		try {
			$dbAdapter->insert('tvshow', $data);
			echo 'adding TV Show : ' . $tvshow->seriesName . PHP_EOL;
		} catch (Exception $e) {
			echo PHP_EOL . 'Entry ' . $id . ' already exists' . PHP_EOL;
		}

		foreach($tvshow->genres as $g) {
			$genreId = $dbAdapter->fetchOne('SELECT id FROM tvshowGenre WHERE name = ?', $g);
			if (!$genreId) {
				$data = array('name' => $g);
				$dbAdapter->insert('tvshowGenre', $data);
				$genreId = $dbAdapter->lastInsertId();
			}
			$data = array(
				'tvshow' => $id,
				'genre' => $genreId
			);
			try {
				$dbAdapter->insert('tvshow_tvshowGenre', $data);
			} catch (Exception $e) {
				// already exists
			}
		}
		foreach($tvshow->actors as $a) {
			$actorId = $dbAdapter->fetchOne('SELECT id FROM actor WHERE name = ?', $a);
			if (!$actorId) {
				$data = array('name' => $a);
				$dbAdapter->insert('actor', $data);
				$actorId = $dbAdapter->lastInsertId();
			}
			$data = array(
				'tvshow' => $id,
				'actor' => $actorId
			);
			try {
				$dbAdapter->insert('tvshow_actor', $data);
			} catch (Exception $e) {
				// already exists
			}
		}

		foreach($tvshow->getSeasons() as $episode) {
			$data = array(
				'id' 			=> $episode->id,
				'tvshow'		=> $tvshow->id,
				'name' 			=> $episode->name,
				'season' 		=> $episode->season,
				'number' 		=> $episode->number,
				'overview' 		=> $episode->overview,
				'first_aired'	=> date('Y-m-d', $episode->firstAired),
				'imdb_id' 		=> $episode->imdbId
			);

			try {
				$dbAdapter->insert('tvshowEpisode', $data);
				echo 'adding Episode S' . str_pad($episode->season, 2, '0', STR_PAD_LEFT) . 'E' . str_pad($episode->number, 2, '0', STR_PAD_LEFT) . ': ' . $episode->name . PHP_EOL;
				$episode_id = $dbAdapter->lastInsertId();

				foreach($episode->guestStars as $a) {
					$actorId = $dbAdapter->fetchOne('SELECT id FROM actor WHERE name = ?', $a);
					if (!$actorId) {
						$data = array('name' => $a);
						$dbAdapter->insert('actor', $data);
						$actorId = $dbAdapter->lastInsertId();
					}
					$data = array(
						'episode' => $episode_id,
						'actor' => $actorId
					);
					try {
						$dbAdapter->insert('tvshowEpisode_guest', $data);
					} catch (Exception $e) {
						// already exists
					}
				}

				foreach($episode->directors as $d) {
					$directorId = $dbAdapter->fetchOne('SELECT id FROM director WHERE name = ?', $d);
					if (!$directorId) {
						$data = array('name' => $d);
						$dbAdapter->insert('director', $data);
						$directorId = $dbAdapter->lastInsertId();
					}
					$data = array(
						'episode' => $episode_id,
						'actor' => $directorId
					);
					try {
						$dbAdapter->insert('tvshowEpisode_director', $data);
					} catch (Exception $e) {
						// already exists
					}
				}

				foreach($episode->writers as $w) {
					$writerId = $dbAdapter->fetchOne('SELECT id FROM writer WHERE name = ?', $w);
					if (!$writerId) {
						$data = array('name' => $w);
						$dbAdapter->insert('writer', $data);
						$writerId = $dbAdapter->lastInsertId();
					}
					$data = array(
						'episode' => $episode_id,
						'writer' => $writerId
					);
					try {
						$dbAdapter->insert('tvshowEpisode_writer', $data);
					} catch (Exception $e) {
						// already exists
					}
				}
			} catch (Exception $e) {
				echo 'Episode S' . str_pad($episode->season, 2, '0', STR_PAD_LEFT) . 'E' . str_pad($episode->number, 2, '0', STR_PAD_LEFT) . ': already exists' . PHP_EOL;

				$episodeName = $dbAdapter->fetchOne('SELECT name FROM tvshowEpisode WHERE id = ?', $episode->id);
				$data = array(
					'id' 			=> $episode->id,
					'tvshow'		=> $tvshow->id,
					'name' 			=> $episode->name,
					'season' 		=> $episode->season,
					'number' 		=> $episode->number,
					'overview' 		=> $episode->overview,
					'first_aired'	=> date('Y-m-d', $episode->firstAired),
					'imdb_id' 		=> $episode->imdbId
				);
				$dbAdapter->update('tvshowEpisode', $data, 'id = ' . $episode->id);
				echo 'Updating episode S' . str_pad($episode->season, 2, '0', STR_PAD_LEFT) . 'E' . str_pad($episode->number, 2, '0', STR_PAD_LEFT) . PHP_EOL;
			}
		}

	}
} catch (Exception $e) {
	echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
	echo $e->getMessage() . PHP_EOL;
	return false;
}

// generally speaking, this script will be run from the command line
return true;



$this->id = (string)$config->id;
$this->season = (string)$config->SeasonNumber;
$this->number = (string)$config->EpisodeNumber;
$this->episode = (string)$config->EpisodeNumber;
$this->firstAired = strtotime((string)$config->FirstAired);
$this->guestStars = $this->removeEmptyIndexes(explode('|', (string)$config->GuestStars));
$this->guestStars = array_map('trim', $this->guestStars);
$this->directors = $this->removeEmptyIndexes(explode('|', (string)$config->Director));
$this->writers = $this->removeEmptyIndexes(explode('|', (string)$config->Writer));
$this->overview = (string)$config->Overview;
$this->imdbId = (string)$config->IMDB_ID;
$this->name = (string)$config->EpisodeName;
