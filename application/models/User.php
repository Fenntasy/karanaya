<?php

class Application_Model_User extends Application_Model_Abstract {
	protected $_mapper;
	
	protected $_id;
	protected $_username;
	protected $_password;
	protected $_display_name;

	public function setDisplayName($displayName) {
		$this->_display_name = (string) $displayName;
		return $this;
	}

	public function getDisplayName() {
		return $this->_display_name;
	}

	public function setUsername($username) {
		$this->_username = (string) $username;
		return $this;
	}

	public function getUsername() {
		return $this->_username;
	}

	public function setPassword($password) {
		$this->_password = (string) $password;
		return $this;
	}

	public function getPassword() {
		return $this->_password;
	}
	
	public function setId($id) {
		$this->_id = (int) $id;
		return $this;
	}

	public function getId() {
		return $this->_id;
	}
	
	public function getMapper() {
		if (is_null($this->_mapper)) {
			$this->_mapper = Application_Model_UserMapper::getInstance();
		}
		return $this->_mapper;
	}

	public function save() {
		$this->getMapper()->save($this);
	}
	
	public function cryptPassword() {
		$this->_password = md5($this->_password);
		return $this->getPassword();
	}
	
	public function delete() {
		$this->getMapper()->delete($this);
	}

}

