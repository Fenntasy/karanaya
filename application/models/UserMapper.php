<?php

class Application_Model_UserMapper {

    private static $_instance = null;
    private static $_userList = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_User';

    private function __construct() {
	
    }

    public static function getInstance() {
	if (is_null(self::$_instance)) {
	    self::$_instance = new Application_Model_UserMapper();
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
	}
	$row = $result->current();

	$user = new Application_Model_User();
	$user->setId($row->id)
		->setDisplayName($row->display_name)
		->setPassword($row->password)
		->setUsername($row->username);

	return $user;
    }

    public function fetchAll() {
	if (is_null(self::$_userList)) {
	    $resultSet = $this->getDbTable()->fetchAll();

	    $entries = array();
	    foreach ($resultSet as $row) {
		$entry = new Application_Model_User();
		$entry->setId($row->id)
		    ->setDisplayName($row->display_name)
		    ->setPassword($row->password)
		    ->setUsername($row->username);
		$entries[$row->id] = $entry;
	    }

	    self::$_userList = $entries;
	}

	return self::$_userList;
    }

    public function save($user) {
	$data = array(
	    'username' => $user->getUsername(),
	    'password' => $user->getPassword(),
	    'display_name' => $user->getDisplayName(),
	);

	if (null === ($id = $user->getId())) {
	    unset($data['id']);
	    $data['password'] = $user->cryptPassword();
	    $id = $this->getDbTable()->insert($data);
	    $user->setId($id);
	} else {
	    $this->getDbTable()->update($data, array('id = ?' => $id));
	}
    }

    public function delete($user) {
	$id = $user->getId();
	$this->getDbTable()->delete(array('id = ?' => $id));
    }

}