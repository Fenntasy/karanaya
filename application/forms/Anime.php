<?php

class Application_Form_Anime extends Zend_Form
{

    public function init()
    {
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
                'required' => true,
                'filters' => array('StringTrim'),
                'label' => 'Name'
            )
        );
        $this->name->addValidator('NotEmpty');

        $this->addElement(
            'text', 'simpleName', array(
                'required' => false,
                'filters' => array('StringTrim'),
                'label' => 'Simple Name'
            )
        );

        $this->addElement(
            'text', 'mal_id', array(
                'required' => false,
                'filters' => array('StringTrim'),
                'label' => 'MAL id'
            )
        );

        $this->addElement(
            'text', 'classification', array(
                'required' => false,
                'filters' => array('StringTrim'),
                'label' => 'Classification'
            )
        );

        $this->addElement(
            'text', 'episodes', array(
                'required' => false,
                'filters' => array('StringTrim'),
                'label' => 'Number of episodes'
            )
        );
        $this->episodes->addValidator('NotEmpty')->addValidator('Digits');

        $this->addElement(
            'select', 'status', array(
                'required' => true,
                'MultiOptions' => array(
                    'finished airing' => 'finished airing',
                    'currently airing' => 'currently airing',
                    'not yet aired' => 'not yet aired'
                ),
                'filters' => array('StringTrim'),
                'label' => 'Classification'
            )
        );

        $this->addElement(
            'textarea', 'synopsis', array(
                'required' => false,
                'filters' => array('StringTrim'),
                'label' => 'Comment',
                'rows' => 8
            )
        );

        $this->addElement('submit', 'submit', array(
                'ignore' => true,
                'label' => 'Save',
            )
        );

    }
}
