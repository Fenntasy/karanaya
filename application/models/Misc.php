<?php

class Application_Model_Misc extends Application_Model_Abstract {
	protected $_mapper;
	
	protected $_id;
	protected $_name;
	protected $_simpleName;

	public function setName($name) {
		$this->_name = (string) $name;
		return $this;
	}

	public function getName() {
		return $this->_name;
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
			$this->_mapper = Application_Model_Mapper_Misc::getInstance();
		}
		return $this->_mapper;
	}

	public function save() {
		$this->getMapper()->save($this);
	}
	
	public function delete() {
		$this->getMapper()->delete($this);
	}

	public function __toArray() {
	    return array(
		'id'		=> $this->getId(),
		'name'		=> $this->getName()
	    );
	}

}

