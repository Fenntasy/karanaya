<?php

class Application_Form_Artist extends Zend_Form {
	
    public function init() {
        $this->setMethod('post');
        
        $this->addElement(
        	'hidden', 'id', array(
        		'required' => false
        	)
        );
	$this->id->setDecorators(array(
	    array('Label', null),
	    array('ViewHelper', null),
	    array('Errors', null),
	    array('Description', array('p' => 'description'))
	));
 
        $this->addElement(
            'text', 'name', array(
                'label' => 'Name:',
                'required' => true,
                'filters'    => array('StringTrim'),
        	)
        );

        $this->addElement(
            'text', 'simpleName', array(
                'label' => 'Simple Name:',
                'required' => false,
                'filters'    => array('StringTrim'),
        	)
        );
 
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Save',
        	)
        );
 
    }
}
