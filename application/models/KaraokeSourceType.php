<?php

class Application_Model_KaraokeSourceType {
	public static $ANIME = 1;
	public static $TVSHOW = 2;
	public static $MOVIE = 3;
	public static $GAME = 4;
	public static $MISC = 5;
	public static $ARTIST = 6;

	private static $_instance = null;

	private function __construct () {

	}

	public static function getInstance () {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function getName ($sourceType) {
		switch ($sourceType) {
			case self::$ANIME:
				return 'anime';
			case self::$GAME:
				return 'game';
			case self::$ARTIST:
				return 'artist';
			case self::$MISC:
				return 'misc';
			case self::$TVSHOW:
				return 'tvshow';
		}
	}

	public function getMapper ($sourceType) {
		switch ($sourceType) {
			case self::$ANIME:
				return Application_Model_AnimeMapper::getInstance();
				break;
			case self::$GAME:
				return Application_Model_Mapper_Game::getInstance();
				break;
			case self::$ARTIST:
				return Application_Model_Mapper_Artist::getInstance();
				break;
			case self::$MISC:
				return Application_Model_Mapper_Misc::getInstance();
				break;
			case self::$TVSHOW:
				return Application_Model_Mapper_Tvshow::getInstance();
				break;
		}
	}

	public function getDirectory ($sourceType) {
		switch ($sourceType) {
			case self::$ANIME:
				return 'Anime';
				break;
			case self::$GAME:
				return 'Games';
				break;
			case self::$MISC:
				return 'Misc';
				break;
			case self::$ARTIST:
				return 'Artists';
				break;
			case self::$TVSHOW:
				return 'Shows';
				break;
		}
	}

	public function getForm ($sourceType) {
		switch ($sourceType) {
			case self::$ANIME:
				return new Application_Form_Karaoke_Anime();
				break;
			case self::$GAME:
				return new Application_Form_Karaoke_Game();
				break;
			case self::$ARTIST:
				return new Application_Form_Karaoke_Artist();
				break;
			case self::$MISC:
				return new Application_Form_Karaoke_Misc();
				break;
			case self::$TVSHOW:
				return new Application_Form_Karaoke_Tvshow();
				break;
		}
	}
}
