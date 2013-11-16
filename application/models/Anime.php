<?php

class Application_Model_Anime extends Application_Model_Abstract {
	protected $_mapper;
	
	protected $_id;
	protected $_name;
	protected $_mal_id;
	protected $_need_update;
	protected $_simpleName;
	protected $_episodes;
	protected $_status;
	protected $_classification;
	protected $_synopsis;
	protected $_tags = array();
	protected $_genres = array();
	protected $_other_titles = array();
	
	public function setOtherTitles($otherTitles) {
		$this->_other_titles = $otherTitles;
		return $this;
	}

	public function getOtherTitles() {
		return $this->_other_titles;
	}
	
	public function setGenres($genres) {
		$this->_genres = $genres;
		return $this;
	}

	public function getGenres() {
		return $this->_genres;
	}
	
	public function setTags($tags) {
		$this->_tags = $tags;
		return $this;
	}

	public function getTags() {
		return $this->_tags;
	}

	public function setSynopsis($synopsis) {
		$this->_synopsis = (string) $synopsis;
		return $this;
	}

	public function getSynopsis() {
		return $this->_synopsis;
	}
	
	public function setClassification($classification) {
		$this->_classification = (string) $classification;
		return $this;
	}

	public function getClassification() {
		return $this->_classification;
	}
	
	public function setStatus($status) {
		$this->_status = (string) $status;
		return $this;
	}

	public function getStatus() {
		return $this->_status;
	}
	
	public function setEpisodes($episodes) {
		$this->_episodes = (int) $episodes;
		return $this;
	}

	public function getEpisodes() {
		return $this->_episodes;
	}

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

    public function setMal_id($id) {
        $this->_mal_id = (int) $id;
        return $this;
    }

    public function getMal_id() {
        return $this->_mal_id;
    }

    public function setNeed_update() {
        $this->_need_update = 1;
        return $this;
    }

    public function getNeed_update() {
        return $this->_need_update;
    }

	public function getId() {
		return $this->_id;
	}
	
	public function getMapper() {
		if (is_null($this->_mapper)) {
			$this->_mapper = Application_Model_AnimeMapper::getInstance();
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
		'id' => $this->getId(),
		'classification' => $this->getClassification(),
        'mal_id' => $this->_mal_id?$this->_mal_id:null,
        'need_update' => $this->_need_update?$this->_need_update:0,
		'episodes' => $this->getEpisodes(),
		'name' => $this->getName(),
		'simpleName' => $this->getSimpleName(),
		'status' => $this->getStatus(),
		'synopsis' => $this->getSynopsis()
	    );
	}

}

