<?php

class Application_Model_Mapper_Misc{

    private static $_instance = null;
    private static $_miscList = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_Misc';

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

	    $game = new Application_Model_Misc();
	    $game->setId($row->id)
		    ->setName($row->name);

	    return $game;
	} else {
	    $entries = array();

	    foreach ($result as $row) {
		$entry = new Application_Model_Misc();
		$entry->setId($row->id)
			->setName($row->name);
		$entries[] = $entry;
	    }

	    return $entries;
	}
    }


    public function fetchAll($ordered = false) {
	if (is_null(self::$_miscList)) {
	    $select = $this->getDbTable()->select()->order('name');
	    $resultSet = $this->getDbTable()->fetchAll($select);

	    $entries = array();
	    foreach ($resultSet as $row) {
		$entry = new Application_Model_Misc();
		$entry->setId($row->id)
			->setName($row->name);
		if ($ordered) {
		    $entries[$row->id] = $entry;
		} else {
		    $entries[] = $entry;
		}
	    }

	    self::$_miscList = $entries;
	}

	return self::$_miscList;
    }

    public function save($misc) {
	$data = $misc->__toArray();

	if (null == ($id = $misc->getId())) {
	    unset($data['id']);
	    $id = $this->getDbTable()->insert($data);
	    $misc->setId($id);
	} else {
	    $this->getDbTable()->update($data, array('id = ?' => $id));
	}
    }

    public function delete($misc) {
	$id = $misc->getId();
	$this->getDbTable()->delete(array('id = ?' => $id));
    }

}
