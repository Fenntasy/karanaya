<?php

class Application_Model_Song extends Application_Model_Abstract {

    protected $_mapper;
    protected $_id;
    protected $_name;
    protected $_simpleName;
    protected $_artist;

    public function setName($name) {
	$this->_name = (string) $name;
	return $this;
    }

    public function getName() {
	return $this->_name;
    }

    public function setSimpleName($name) {
	$this->_simpleName = $name;
	return $this;
    }

    public function getSimpleName() {
	return $this->_simpleName;
    }

    public function setId($id) {
	$this->_id = (int) $id;
	return $this;
    }

    public function getId() {
	return $this->_id;
    }

    public function setArtist($artist) {
	if (is_numeric($artist)) {
	    $artists = Application_Model_Mapper_Artist::getInstance()->fetchAll(true);
	    $this->_artist = $artists[$artist];
	} else if ($artist instanceof Application_Model_Artist) {
	    $this->_artist = $artist;
	} else {
	    $this->_artist = new Application_Model_Artist();
	}
	return $this;
    }

    public function getArtist() {
	return $this->_artist;
    }

    public function getMapper() {
	if (is_null($this->_mapper)) {
	    $this->_mapper = Application_Model_SongMapper::getInstance();
	}
	return $this->_mapper;
    }

    public function save() {
	$this->getMapper()->save($this);
    }

    public function delete() {
	$this->getMapper()->delete($this);
    }

    public function __toArray($saving = false) {
	$array = array(
	    'id' => $this->getId(),
	    'name' => $this->getName(),
	    'simpleName' => ($this->getSimpleName() ? $this->getSimpleName() : null),
	    'artist' => ($this->getArtist() ? $this->getArtist()->getId() : null)
	);
	if (!$saving) {
	    $array['artist_name'] = ($this->getArtist() ? $this->getArtist()->getName() : null);
	}
	return $array;
    }

}

