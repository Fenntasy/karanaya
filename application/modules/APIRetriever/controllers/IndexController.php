<?php

class APIRetriever_IndexController extends Zend_Controller_Action {

    public function init() {
	$this->_helper->layout->disableLayout();
    }

    public function indexAction() {
	
    }

    public function searchAction() {
	$retriever = new APIRetriever_Model_IMDB();
	$this->view->retriever = $retriever->search('how i met your mother');
    }

}

