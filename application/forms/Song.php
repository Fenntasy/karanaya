<?php

class Application_Form_Song extends Zend_Form {
	
    public function init() {
        $this->setMethod('post');
        
        $this->addElement(
        	'hidden', 'id', array(
        		'required' => false
        	)
        );
 
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
	
	$this->addElement(
	    'text', 'artist_name', array(
		'required'	=> false,
		'label'		=> 'Artist'
	    )
	);

	$this->addElement(
	    'hidden', 'artist', array(
		'required'	=> false
	    )
	);

        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Save',
        	)
        );
 
    }
}
