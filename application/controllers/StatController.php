<?php

class StatController extends Application_Plugin_Action_Auth
{

	public function init() {
	}

	public function indexAction() {
	}

	public function showAction() {
		$karaCount = 0;
		$karaDuration = 0;

		//SubTypes
		$ass1 = 0;
		$ass1Duration = 0;
		$ass2 = 0;
		$ass2Duration = 0;
		$srt = 0;
		$srtDuration = 0;
		$incrusted = 0;
		$incrustedDuration = 0;
		$voiceless = 0;
		$voicelessDuration = 0;

		//SourceTypes
		$anime = 0;
		$animeDuration = 0;
		$show = 0;
		$showDuration = 0;
		$movie = 0;
		$movieDuration = 0;
		$game = 0;
		$gameDuration = 0;
		$misc = 0;
		$miscDuration = 0;
		$artist = 0;
		$artistDuration = 0;

		$authors = array();
		$authorList = Application_Model_UserMapper::getInstance()->fetchAll();
		$authorList[0] = new Application_Model_User(array('displayName' => 'Other'));
		$db = new Application_Model_DbTable_Karaoke();
		$resultSet = $db->fetchAll();
		$entries = array();
		foreach ($resultSet as $row) {
			switch($row->subType) {
			case 1:
				$ass1++;
				$ass1Duration += $row->duration;
				break;
			case 2:
				$ass2++;
				$ass2Duration += $row->duration;
				break;
			case 3:
				$srt++;
				$srtDuration += $row->duration;
				break;
			case 4:
				$incrusted++;
				$incrustedDuration += $row->duration;
				break;
			case 5:
				$voiceless++;
				$voicelessDuration += $row->duration;
				break;
			}
			switch($row->sourceType) {
			case 1:
				$anime++;
				$animeDuration += $row->duration;
				break;
			case 2:
				$show++;
				$showDuration += $row->duration;
				break;
			case 3:
				$movie++;
				$movieDuration += $row->duration;
				break;
			case 4:
				$game++;
				$gameDuration += $row->duration;
				break;
			case 5:
				$misc++;
				$miscDuration += $row->duration;
				break;
			case 6:
				$artist++;
				$artistDuration += $row->duration;
				break;
			}
			if (!isset($authors[$authorList[(int)$row->madeBy]->getDisplayName()])) {
				$authors[$authorList[(int)$row->madeBy]->getDisplayName()] = 0;
			}
			$authors[$authorList[(int)$row->madeBy]->getDisplayName()]++;
			$karaCount++;
			$karaDuration += $row->duration;
		}


		$this->view->karaCount = $karaCount;
		$this->view->karaDuration = $karaDuration;

		//SubTypes
		$this->view->ass1 = $ass1;
		$this->view->ass1Duration = $ass1Duration;
		$this->view->ass2 = $ass2;
		$this->view->ass2Duration = $ass2Duration;
		$this->view->srt = $srt;
		$this->view->srtDuration = $srtDuration;
		$this->view->incrusted = $incrusted;
		$this->view->incrustedDuration = $incrustedDuration;
		$this->view->voiceless = $voiceless;
		$this->view->voicelessDuration = $voicelessDuration;

		//SourceTypes
		$this->view->anime = $anime;
		$this->view->animeDuration = $animeDuration;
		$this->view->show = $show;
		$this->view->showDuration = $showDuration;
		$this->view->movie = $movie;
		$this->view->movieDuration = $movieDuration;
		$this->view->game = $game;
		$this->view->gameDuration = $gameDuration;
		$this->view->misc = $misc;
		$this->view->miscDuration = $miscDuration;
		$this->view->artist = $artist;
		$this->view->artistDuration = $artistDuration;

		$this->view->authors = $authors;
	}


}
