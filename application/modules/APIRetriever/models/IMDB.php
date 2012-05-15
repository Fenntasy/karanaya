<?php

class APIRetriever_Model_IMDB extends Application_Model_Abstract implements APIRetriever_Model_Interface {
    private $_apiUrl = 'http://imdbapi.com/';
    private $_search;

    public function search($name) {
	$name = str_replace(' ', '+', $name);
//	$name = urlencode($name);
	$this->_search = json_decode(file_get_contents($this->_apiUrl . '?t=%' . $name . '%'));
	return $this;
    }

    public function fetch($id) {

    }

    public function getData() {
	
    }
}
