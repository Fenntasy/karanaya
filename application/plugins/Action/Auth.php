<?php

class Application_Plugin_Action_Auth extends Zend_Controller_Action {

    public function preDispatch() {
	$auth = Zend_Auth::getInstance();
	if ($auth->hasIdentity()) {
	    // Identity exists; get it
	    $this->view->identity = $auth->getIdentity();
	    $session = new Zend_Session_Namespace('karanaya');
	    $this->view->user = $session->user;
	} else {
	    $loginForm = new Application_Form_Auth_Login($_POST);
	    $loginForm->setAction('/Auth/login');
	    $this->view->loginForm = $loginForm;
	}
	$this->view->render('user/_sidebar.phtml');
    }

}

