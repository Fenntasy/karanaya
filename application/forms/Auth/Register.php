<?php

class Application_Form_Auth_Register extends Zend_Form {

    public function init() {
	$this->setMethod('post');

	$this->addElement('text', 'username', array(
	    'label' => 'Username:',
	    'required' => true,
	    'filters' => array('StringTrim')
	));

	$this->addElement('text', 'displayName', array(
	    'label' => 'Displayed Name:',
	    'required' => true,
	    'filters' => array('StringTrim')
	));

	$this->addElement('password', 'password', array(
	    'label' => 'Password:',
	    'required' => true
	));

	$this->addElement('password', 'password_verification', array(
	    'label' => 'Retype your Password:',
	    'required' => true
	));

	$this->addElement('submit', 'submit', array(
	    'ignore' => true,
	    'label' => 'Login'
	));

//		$this->addElement('hash', 'csrf', array(
//			'ignore' => true
//		));
    }

}

