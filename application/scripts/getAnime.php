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
    'id|i=i' 	=> 'MAL id of anime',
    'list|l'    => 'Get the last id tested',
    'update|u'   => 'Update entries that aren\'t finished airing',
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
if ($getopt->getOption('h') || !($getopt->getOption('i') || $getopt->getOption('l'))) {
    echo $getopt->getUsageMessage();
    return true;
}

// Initialize values based on presence or absence of CLI options
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

if ($getopt->getOption('l')) {
    $lastId = $dbAdapter->fetchOne('SELECT mal_id FROM anime ORDER BY mal_id DESC');
    echo $lastId . "\n";
    return true;
}
 
// this block executes the actual retrieval of anime infor
try {
    try {
	    $mal_infos =  file_get_contents('http://mal-api.com/anime/' . urlencode($id));
    } catch(ErrorException $e) {
        echo  PHP_EOL . 'There is no entry ' . $id . ' in myanimelist' . PHP_EOL;
        exit(0);
    }
	if ($mal_infos) {
		$mal_infos = json_decode($mal_infos);

		$data = array(
		    'mal_id'			=> $mal_infos->id,
		    'name'				=> $mal_infos->title,
		    'type'				=> $mal_infos->type,
			'episodes'			=> $mal_infos->episodes,
			'status'			=> $mal_infos->status,
			'classification'	=> $mal_infos->classification,
			'synopsis'			=> $mal_infos->synopsis
		);
		
		try {
			$dbAdapter->insert('anime', $data);
			$anime_id = $dbAdapter->fetchOne('SELECT id FROM anime WHERE mal_id = ?', $mal_infos->id);
		} catch (Exception $e) {
			echo PHP_EOL . 'Entry ' . $id . '(' . $mal_infos->title . ') already exists, updating...' . PHP_EOL;
			$dbAdapter->update('anime', $data, 'mal_id = ' . $mal_infos->id);
			$select = new Zend_Db_Select($dbAdapter);
			$anime_id = $dbAdapter->fetchOne('SELECT id FROM anime WHERE mal_id = ?', $mal_infos->id);
		}
		
		foreach($mal_infos->other_titles as $type => $titles) {
			$data = array(
				'anime'	=> $anime_id,
				'type'	=> $type
			);
			foreach($titles as $title) {
				$data['title']	= $title;
				try {
					$dbAdapter->insert('anime_otherTitle', $data);
				} catch (Exception $e) {
					// already exists
				}
			}
		}
		
		foreach($mal_infos->genres as $g) {
			$genreId = $dbAdapter->fetchOne('SELECT id FROM animeGenre WHERE name = ?', $g);
			if (!$genreId) {
				$data = array('name' => $g);
				$dbAdapter->insert('animeGenre', $data);
				$genreId = $dbAdapter->lastInsertId();
			}
			$data = array(
				'anime' => $anime_id,
				'genre' => $genreId
			);
			try {
				$dbAdapter->insert('anime_animeGenre', $data);
			} catch (Exception $e) {
				// already exists
			}
		}
		
		foreach($mal_infos->tags as $t) {
			$tagId = $dbAdapter->fetchOne('SELECT id FROM animeTag WHERE name = ?', $t);
			if (!$tagId) {
				$data = array('name' => $t);
				$dbAdapter->insert('animeTag', $data);
				$tagId = $dbAdapter->lastInsertId();
			}
			$data = array(
				'anime' => $anime_id,
				'tag' => $tagId
			);
			try {
				$dbAdapter->insert('anime_animeTag', $data);
			} catch (Exception $e) {
				// already exists
			}
		}
		
		foreach($mal_infos->side_stories as $st) {
			$data = array(
				'anime' => $anime_id,
				'sideStory' => $st->anime_id
			);
			try {
				$dbAdapter->insert('anime_sideStory', $data);
			} catch (Exception $e) {
				// already exists
			}
		}
		
		foreach($mal_infos->sequels as $s) {
			$data = array(
				'anime' => $anime_id,
				'sequel' => $s->anime_id
			);
			try {
				$dbAdapter->insert('anime_sequel', $data);
			} catch (Exception $e) {
				// already exists
			}
		}
		
		foreach($mal_infos->prequels as $p) {
			$data = array(
				'anime' =>  $anime_id,
				'prequel' => $p->anime_id
			);
			try {
				$dbAdapter->insert('anime_prequel', $data);
			} catch (Exception $e) {
				// already exists
			}
		}

        try {
            // saving MAL image (as default)
            $serverImg = file_get_contents($mal_infos->image_url);
            file_put_contents(APPLICATION_PATH . '../public/images/anime/' . $anime_id . '.jpg', $serverImg);
        } catch (Exception $e) {
            echo PHP_EOL . 'There is no image for entry ' . $id . PHP_EOL;
        }
	} else {
		if ($http_response_header[0] !=	"HTTP/1.1 404 Not Found") {
			file_put_contents(APPLICATION_PATH . '../logs/timeout', file_get_contents(APPLICATION_PATH . '../logs/timeout') . "\n" . $id);
		}
	}
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}
 
// generally speaking, this script will be run from the command line
return true;
