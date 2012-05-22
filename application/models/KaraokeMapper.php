<?php

class Application_Model_KaraokeMapper {

    private static $_instance = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_Karaoke';

    private function __construct() {
	
    }

    public static function getInstance() {
	if (is_null(self::$_instance)) {
	    self::$_instance = new Application_Model_KaraokeMapper();
	}

	return self::$_instance;
    }

    public function setDbTable($dbTable) {
	if (is_string($dbTable)) {
	    $dbTable = new $dbTable();
	}
	if (!$dbTable instanceof Zend_Db_Table_Abstract) {
	    throw new Exception('Invalid table data gateway provided');
	}
	$this->_dbTable = $dbTable;
	return $this;
    }

    public function getDbTable() {
	if (null === $this->_dbTable) {
	    $this->setDbTable($this->_dbTableName);
	}
	return $this->_dbTable;
    }

    public function find($id) {
	$result = $this->getDbTable()->find($id);

	if (0 == count($result)) {
	    return;
	} else if (1 == count($result)) {
	    $row = $result->current();

	    $karaoke = new Application_Model_Karaoke();
	    $karaoke->setId($row->id)
		    ->setVersion($row->version)
		    ->setType($row->type)
		    ->setTypeNumber($row->typeNumber)
		    ->setSubType($row->subType)
		    ->setMadeBy($row->madeBy)
		    ->setAddedBy($row->addedBy)
		    ->setSong($row->song)
		    ->setSourceType($row->sourceType)
		    ->setSource($row->source)
			->setSubSource($row->subSource)
		    ->setLanguage($row->language)
		    ->setDuration($row->duration)
		    ->setExtension($row->extension)
		    ->setComment($row->comment);

	    return $karaoke;
	} else {
	    $entries = array();

	    foreach ($result as $row) {
		$entry = new Application_Model_Karaoke();
		$entry->setId($row->id)
			->setVersion($row->version)
			->setType($row->type)
			->setTypeNumber($row->typeNumber)
			->setSubType($row->subType)
			->setMadeBy($row->madeBy)
			->setAddedBy($row->addedBy)
			->setSong($row->song)
			->setSourceType($row->sourceType)
			->setSource($row->source)
			->setSubSource($row->subSource)
			->setLanguage($row->language)
			->setDuration($row->duration)
			->setExtension($row->extension)
			->setComment($row->comment);
		$entries[] = $entry;
	    }

	    return $entries;
	}
    }

    public function getTotalRecords($name = '') {
	$db = Zend_Db_Table::getDefaultAdapter();
	$select = $db->select()->from('karaoke', 'COUNT(*)');
	if ($name) {
	    $select->joinLeft('anime', 'karaoke.sourceType = 1 AND anime.id = karaoke.source', '')
			->joinLeft('game', 'karaoke.sourceType = 4 AND game.id = karaoke.source', '')
			->joinLeft('artist', 'karaoke.sourceType = 6 AND artist.id = karaoke.source', '')
			->joinLeft('song', 'song.id = karaoke.song', '')
			->where('anime.name LIKE ?', '%' . $name . '%')
			->orWhere('anime.simpleName LIKE ?', '%' . $name . '%')
			->orWhere('game.name LIKE ?', '%' . $name . '%')
			->orWhere('artist.name LIKE ?', '%' . $name . '%')
			->orWhere('song.name LIKE ?', '%' . $name . '%')
			->orWhere('song.simpleName LIKE ?', '%' . $name . '%');
	}

	return $db->fetchOne($select);
    }

    public function findByName($name, $start = 0, $length = 0) {
	if (empty($name)) {
	    return $this->fetchAll();
	}
	$select = $this->getDbTable()->select()
			->from('karaoke')
			->joinLeft('anime', 'karaoke.sourceType = 1 AND anime.id = karaoke.source', '')
			->joinLeft('game', 'karaoke.sourceType = 4 AND game.id = karaoke.source', '')
			->joinLeft('artist', 'karaoke.sourceType = 6 AND artist.id = karaoke.source', '')
			->joinLeft('song', 'song.id = karaoke.song', '')
			->where('anime.name LIKE ?', '%' . $name . '%')
			->orWhere('anime.simpleName LIKE ?', '%' . $name . '%')
			->orWhere('game.name LIKE ?', '%' . $name . '%')
			->orWhere('artist.name LIKE ?', '%' . $name . '%')
			->orWhere('song.name LIKE ?', '%' . $name . '%')
			->orWhere('song.simpleName LIKE ?', '%' . $name . '%');

	if ($start || $length) {
	    $select = $select->limit($length, $start);
	}

	$resultSet = $this->getDbTable()->fetchAll($select);
	$entries = array();

	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Karaoke();
	    $entry->setId($row->id)
		    ->setVersion($row->version)
		    ->setType($row->type)
		    ->setTypeNumber($row->typeNumber)
		    ->setSubType($row->subType)
		    ->setMadeBy($row->madeBy)
		    ->setAddedBy($row->addedBy)
		    ->setSong($row->song)
		    ->setSourceType($row->sourceType)
		    ->setSource($row->source)
			->setSubSource($row->subSource)
		    ->setLanguage($row->language)
		    ->setDuration($row->duration)
		    ->setExtension($row->extension)
		    ->setComment($row->comment);
	    $entries[] = $entry;
	}
	return $entries;
    }

    public function findBySong($song) {
	$select = $this->getDbTable()->select()
			->from('karaoke')
			->joinLeft('song', 'song.id = karaoke.song', '')
			->where('song.id = ?', $song);

	$resultSet = $this->getDbTable()->fetchAll($select);
	$entries = array();

	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Karaoke();
	    $entry->setId($row->id)
		    ->setVersion($row->version)
		    ->setType($row->type)
		    ->setTypeNumber($row->typeNumber)
		    ->setSubType($row->subType)
		    ->setMadeBy($row->madeBy)
		    ->setAddedBy($row->addedBy)
		    ->setSong($row->song)
		    ->setSourceType($row->sourceType)
		    ->setSource($row->source)
			->setSubSource($row->subSource)
		    ->setLanguage($row->language)
		    ->setDuration($row->duration)
		    ->setExtension($row->extension)
		    ->setComment($row->comment);
	    $entries[] = $entry;
	}
	return $entries;
    }

    public function fetchAll($ordered = false, $start = 0, $length = 0) {
	$select = $this->getDbTable()->select();
	if ($start || $length) {
	    $select = $select->limit($length, $start);
	}

	$resultSet = $this->getDbTable()->fetchAll($select);

	$entries = array();
	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Karaoke();
	    $entry->setId($row->id)
		    ->setVersion($row->version)
		    ->setType($row->type)
		    ->setTypeNumber($row->typeNumber)
		    ->setSubType($row->subType)
		    ->setMadeBy($row->madeBy)
		    ->setAddedBy($row->addedBy)
		    ->setSong($row->song)
		    ->setSourceType($row->sourceType)
		    ->setSource($row->source)
			->setSubSource($row->subSource)
		    ->setLanguage($row->language)
		    ->setDuration($row->duration)
		    ->setExtension($row->extension)
		    ->setComment($row->comment);
	    $entries[] = $entry;
	}
	return $entries;
    }

    public function save($karaoke) {
	$data = $karaoke->__toArray(true);
	$auth = Zend_Auth::getInstance();
	if ($auth->hasIdentity()) {
	    $this->view->identity = $auth->getIdentity();
	    $session = new Zend_Session_Namespace('karanaya');
	    $data['addedBy'] = $session->user->getId();
	} else {
	    return false;
	}
	if ($data['madeBy'] == 0) {
	    $data['madeBy'] = null;
	}

	if (null == ($id = $karaoke->getId())) {
	    unset($data['id']);
	    $data['creation_date'] = new Zend_Db_Expr('NOW()');
	    $id = $this->getDbTable()->insert($data);
	    $karaoke->setId($id);
	} else {
	    $data['last_modification_date'] = new Zend_Db_Expr('NOW()');
	    $this->getDbTable()->update($data, array('id = ?' => $id));
	}
	return true;
    }

    public function delete($video) {
	$id = $video->getId();
	$this->getDbTable()->delete(array('id = ?' => $id));
    }

}