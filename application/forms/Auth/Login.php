<?php

class Application_Form_Auth_Login extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
 
        $this->addElement(
            'text', 'username', array(
                'label'	    => 'Username:',
                'required'  => true,
                'filters'   => array('StringTrim'),
		'size'	    => 10
            ));
 
        $this->addElement('password', 'password', array(
            'label'	=> 'Password:',
            'required'	=> true,
	    'size'	=> 10
        ));
 
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
            ));
 
    }
}
