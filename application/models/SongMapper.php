<?php

class Application_Model_SongMapper {

    private static $_instance = null;
    private static $_songList = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_Song';

    private function __construct() {
	
    }

    public static function getInstance() {
	if (is_null(self::$_instance)) {
	    self::$_instance = new Application_Model_SongMapper();
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

	    $song = new Application_Model_Song();
	    $song->setId($row->id)
		    ->setName($row->name)
		    ->setSimpleName($row->simpleName)
		    ->setArtist($row->artist);

	    return $song;
	} else {
	    $entries = array();

	    foreach ($result as $row) {
		$entry = new Application_Model_Song();
		$entry->setId($row->id)
			->setName($row->name)
			->setSimpleName($row->simpleName)
			->setArtist($row->artist);
		$entries[] = $entry;
	    }

	    return $entries;
	}
    }

    public function findByName($name, $start = 0, $length = 0) {
	if (empty($name)) {
	    return $this->fetchAll();
	}
	$select = $this->getDbTable()->select()
			->where('name LIKE ?', '%' . $name . '%');

	if ($start || $length) {
	    $select = $select->limit($length, $start);
	}

	$resultSet = $this->getDbTable()->fetchAll($select);
	$entries = array();

	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Song();
	    $entry->setId($row->id)
		  ->setName($row->name)
		  ->setSimpleName($row->simpleName)
		  ->setArtist($row->artist);
	    $entries[] = $entry;
	}

	return $entries;
    }

    public function findByArtist($artistId) {
	$select = $this->getDbTable()->select()
			->where('artist = ?', $artistId);

	$resultSet = $this->getDbTable()->fetchAll($select);
	$entries = array();

	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Song();
	    $entry->setId($row->id)
		    ->setName($row->name)
		    ->setSimpleName($row->simpleName)
		    ->setArtist($row->artist);
	    $entries[] = $entry;
	}

	return $entries;
    }

    public function fetchAll($ordered = false, $start = 0, $length = 0) {
	if (is_null(self::$_songList)) {
	    $resultSet = $this->getDbTable()->fetchAll();

	    $entries = array();
	    foreach ($resultSet as $row) {
		$entry = new Application_Model_Song();
		$entry->setId($row->id)
			->setName($row->name)
			->setSimpleName($row->simpleName);
		if ($ordered) {
		    $entries[$row->id] = $entry;
		} else {
		    $entries[] = $entry;
		}
	    }

	    self::$_songList = $entries;
	}

	return self::$_songList;
    }

    public function save($song) {
	$data = $song->__toArray(true);

	if (null == ($id = $song->getId())) {
	    unset($data['id']);
	    $id = $this->getDbTable()->insert($data);
	    $song->setId($id);
	} else {
	    $this->getDbTable()->update($data, array('id = ?' => $id));
	}
    }

    public function delete($song) {
	$id = $song->getId();
	$this->getDbTable()->delete(array('id = ?' => $id));
    }

}