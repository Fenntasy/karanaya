<?php

class Application_Model_TvshowEpisode extends Application_Model_Abstract {
	protected $_mapper;
	
	protected $_id;
	protected $_name;
	protected $_simpleName;
	protected $_songs = array();
	
	
	public function setSimpleName($name) {
		$this->_simpleName = (string) $name;
		return $this;
	}

	public function getSimpleName() {
		return $this->_simpleName;
	}

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
			$this->_mapper = Application_Model_Mapper_TvshowEpisode::getInstance();
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
		'name'		=> $this->getName(),
		'simpleName'	=> $this->getSimpleName(),
		'id'		=> $this->getId()
	    );
	}

}

