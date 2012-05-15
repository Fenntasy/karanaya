<?php

class Application_Model_Mapper_Artist {

    private static $_instance = null;
    private static $_artistList = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_Artist';

    private function __construct() {

    }

    public static function getInstance() {
	if (is_null(self::$_instance)) {
	    self::$_instance = new self();
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

	    $artist = new Application_Model_Artist();
	    $artist->setId($row->id)
		    ->setName($row->name)
            ->setSimpleName($row->simpleName);

	    $artist->setSongs(Application_Model_SongMapper::getInstance()->findByArtist($id));

	    return $artist;
	} else {
	    $entries = array();

	    foreach ($result as $row) {
		$entry = new Application_Model_Artist();
		$entry->setId($row->id)
			->setName($row->name)
            ->setSimpleName($row->simpleName);
		$entry->setSongs(Application_Model_SongMapper::getInstance()->findByArtist($row->id));
		$entries[] = $entry;
	    }

	    return $entries;
	}
    }

    public function findByName($name, $start = 0, $length = 0, $strict = false) {
	if (empty($name)) {
	    return $this->fetchAll();
	}
	$select = $this->getDbTable()->select();
	if ($strict) {
	    $select->where('name LIKE ?', $name);
	} else {
	    $select->where('name LIKE ?', '%' . $name . '%');
	}

	if ($start || $length) {
	    $select = $select->limit($length, $start);
	}

	$resultSet = $this->getDbTable()->fetchAll($select);
	$entries = array();

	foreach ($resultSet as $row) {
	    $entry = new Application_Model_Artist();
	    $entry->setId($row->id)
		    ->setName($row->name)
            ->setSimpleName($row->simpleName);
	    $entries[] = $entry;
	}

	return $entries;
    }

    public function fetchAll($ordered = false) {
	if (is_null(self::$_artistList)) {
	    $select = $this->getDbTable()->select()->order('name');
	    $resultSet = $this->getDbTable()->fetchAll($select);

	    $entries = array();
	    foreach ($resultSet as $row) {
		$entry = new Application_Model_Artist();
		$entry->setId($row->id)
			->setName($row->name)
			->setSimpleName($row->simpleName);
		if ($ordered) {
		    $entries[$row->id] = $entry;
		} else {
		    $entries[] = $entry;
		}
	    }

	    self::$_artistList = $entries;
	}

	return self::$_artistList;
    }

    public function save($artist) {
	$data = $artist->__toArray();

	if (null == ($id = $artist->getId())) {
	    unset($data['id']);
	    $id = $this->getDbTable()->insert($data);
	    $artist->setId($id);
	} else {
	    $this->getDbTable()->update($data, array('id = ?' => $id));
	}
    }

    public function delete($artist) {
	$id = $artist->getId();
	$this->getDbTable()->delete(array('id = ?' => $id));
    }

}