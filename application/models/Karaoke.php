<?php

class Application_Model_Karaoke extends Application_Model_Abstract {

	protected $_mapper;
	protected $_id;
	protected $_version;
	protected $_type;
	protected $_typeNumber;
	protected $_subType;
	protected $_madeBy;
	protected $_addedBy;
	protected $_song;
	protected $_source;
	protected $_subSource;
	protected $_sourceType;
	protected $_language;
	protected $_duration;
	protected $_extension;
	protected $_comment;

	public function setType ($type) {
		$typeMapper = new Application_Model_DbTable_Type();
		$select = $typeMapper->select()->where('id = ?', $type);
		$this->_type = $typeMapper->fetchRow($select);
		return $this;
	}

	public function getType () {
		return $this->_type;
	}

	public function setTypeNumber ($typeNumber) {
		$this->_typeNumber = $typeNumber;
		return $this;
	}

	public function getTypeNumber () {
		return $this->_typeNumber;
	}

	public function setSubType ($subType) {
		$subTypeMapper = new Application_Model_DbTable_Subtype();
		$select = $subTypeMapper->select()->where('id = ?', $subType);
		$this->_subType = $subTypeMapper->fetchRow($select);
		return $this;
	}

	public function getSubType () {
		return $this->_subType;
	}

	public function setMadeBy ($madeBy) {
		if (is_numeric($madeBy)) {
			$users = Application_Model_UserMapper::getInstance()->fetchAll(true);
			$this->_madeBy = $users[$madeBy];
		} else if ($madeBy instanceof Application_Model_User) {
			$this->_madeBy = $madeBy;
		} else {
			$this->_madeBy = null;
		}
		return $this;
	}

	public function getMadeBy () {
		return $this->_madeBy;
	}

	public function setAddedBy ($addedBy) {
		if (is_numeric($addedBy)) {
			$users = Application_Model_UserMapper::getInstance()->fetchAll(true);
			$this->_addedBy = $users[$addedBy];
		} else if ($addedBy instanceof Application_Model_User) {
			$this->_addedBy = $addedBy;
		} else {
			$this->_addedBy = null;
		}
		return $this;
	}

	public function getAddedBy () {
		return $this->_addedBy;
	}

	public function setComment ($comment) {
		$this->_comment = $comment;
		return $this;
	}

	public function getComment () {
		return $this->_comment;
	}

	public function setExtension ($extension) {
		$this->_extension = $extension;
		return $this;
	}

	public function getExtension () {
		return $this->_extension;
	}

	public function setDuration ($duration) {
		if (!is_int($duration) && !ctype_digit($duration)) {
			if (strstr($duration, '.')) {
				list($minutes, $seconds) = explode('.', $duration);
			} else if (strstr($duration, ':')) {
				list($minutes, $seconds) = explode(':', $duration);
			} else if (strstr($duration, '/')) {
				list($minutes, $seconds) = explode('/', $duration);
			}
			$duration = 60 * $minutes + $seconds;
		}
		$this->_duration = (int)$duration;
		return $this;
	}

	public function getDuration () {
		return $this->_duration;
	}

	public function getHumanDuration () {
		return floor($this->getDuration() / 60) . ':' . ($this->getDuration() % 60);
	}

	public function setLanguage ($language) {
		$langMapper = new Application_Model_DbTable_Language();
		$select = $langMapper->select()->where('id = ?', $language);
		$this->_language = $langMapper->fetchRow($select);
		return $this;
	}

	public function getLanguage () {
		return $this->_language;
	}

	public function setSourceType ($sourceType) {
		$this->_sourceType = $sourceType;
		return $this;
	}

	public function getSourceType () {
		return $this->_sourceType;
	}

	public function setSource ($source) {
		if (is_numeric($source)) {
			$sources = Application_Model_KaraokeSourceType::getInstance()->getMapper($this->getSourceType())->fetchAll(true, 0, 0);
			$this->_source = $sources[$source];
		} else if ($source instanceof Application_Model_Abstract) {
			$this->_source = $source;
		} else {
			$this->_source = null;
		}
		return $this;
	}

	public function getSource () {
		return $this->_source;
	}

	public function setSubSource ($subSource) {
		if (is_numeric($subSource)) {
			$this->_subSource = Application_Model_Mapper_TvshowEpisode::getInstance()->find($subSource);
		} else if ($subSource instanceof Application_Model_Abstract) {
			$this->_subSource = $subSource;
		} else {
			$this->_subSource = null;
		}
		return $this;
	}

	public function getSubSource() {
		return $this->_subSource;
	}

	public function setSong ($song) {
		if (is_numeric($song)) {
			$songs = Application_Model_SongMapper::getInstance()->fetchAll(true);
			$this->_song = $songs[$song];
		} else if ($song instanceof Application_Model_Song) {
			$this->_song = $song;
		} else {
			$this->_song = null;
		}
		return $this;
	}

	public function getSong () {
		return $this->_song;
	}

	public function setVersion ($version) {
		$this->_version = (string)$version;
		return $this;
	}

	public function getVersion () {
		return $this->_version;
	}

	public function setId ($id) {
		$this->_id = (int)$id;
		return $this;
	}

	public function getId () {
		return $this->_id;
	}

	public function getBaseName () {
		return (
			method_exists($this->getSource(), 'getSimpleName') && $this->getSource()->getSimpleName() ? $this->getSource()->getSimpleName() : $this->getSource()->getName())
			. ($this->getSubSource() ? ' ' . $this->getSubSource()->getSimpleName() : '')
			. ' - ' . $this->getType()->identifier
			. (($this->getTypeNumber()) ? $this->getTypeNumber() : '')
			. ' - ' . ($this->getSong()->getSimpleName() ? $this->getSong()->getSimpleName() : $this->getSong()->getName())
			. (($this->getVersion()) ? ' {' . $this->getVersion() . '}' : '')
			. (($this->getSubType()->name == 'incrusted' || $this->getSubType()->name == 'voiceless') ? ' (' . $this->getSubType()->name . ')' : '');
	}

	public function getFilename () {
		return $this->getBaseName() . '.' . $this->getExtension();
	}

	public function getSubFilename () {
		$sub = $this->getBaseName() . '.';
		switch ($this->getSubType()->name) {
			case 'ASS1':
			case 'ASS2':
				$sub .= 'ass';
				break;
			case 'SRT':
				$sub .= 'srt';
				break;
			default:
				return null;
		}
		return $sub;
	}

	public function checkSubExists ($directory) {
		if ($this->getSubFilename()) {
			return file_exists($directory . '/' . $this->getSubFilename());
		}
		return true;
	}

	public function checkVideoExists ($directory) {
		return file_exists($directory . '/' . $this->getFilename());
	}

	public function getMapper () {
		if (is_null($this->_mapper)) {
			$this->_mapper = Application_Model_KaraokeMapper::getInstance();
		}
		return $this->_mapper;
	}

	public function save () {
		return $this->getMapper()->save($this);
	}

	public function delete () {
		$this->getMapper()->delete($this);
	}

	public function __toArray ($saving = false) {
		$array = array(
			'id' => $this->getId(),
			'addedBy' => ($this->getAddedBy() ? $this->getAddedBy()->getId() : 0),
			'source' => $this->getSource()->getId(),
			'subSource' => (($this->getSubSource())?$this->getSubSource()->getId(): 0),
			'sourceType' => $this->getSourceType(),
			'comment' => $this->getComment(),
			'duration' => $this->getDuration(),
			'extension' => $this->getExtension(),
			'language' => (int)$this->getLanguage()->id,
			'madeBy' => ($this->getMadeBy() ? $this->getMadeBy()->getId() : 0),
			'version' => $this->getVersion(),
			'song' => $this->getSong()->getid(),
			'subType' => (int)$this->getSubType()->id,
			'type' => (int)$this->getType()->id,
			'typeNumber' => $this->getTypeNumber());
		if (!$saving) {
			$sourceType = Application_Model_KaraokeSourceType::getInstance()->getName($this->getSourceType());
			$array[$sourceType . '_name'] = ($this->getSource()) ? $this->getSource()->getName() : null;
			$array['tvshowEpisode_name'] = ($this->getSubSource()) ? $this->getSubSource()->getSimpleName() . ' - ' . $this->getSubSource()->getName() : null;
			$array['song_name'] = ($this->getSong()) ? $this->getSong()->getName() : null;
			$array['duration'] = $this->getHumanDuration();
		}
		return $array;
	}

}

