<?php

class Application_Form_Karaoke_Tvshow extends Zend_Form {

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
			'hidden', 'sourceType', array(
				'required'  => true,
				'value'	    => Application_Model_KaraokeSourceType::$TVSHOW
			)
		);
		$this->sourceType->setDecorators(array(
			array('Label', null),
			array('ViewHelper', null),
			array('Errors', null),
			array('Description', array('p' => 'description'))
		));


		$this->addElement(
			'text', 'tvshow_name', array(
				'required'  => true,
				'filters'   => array('StringTrim'),
				'label'	    => 'Tv Show',
				'size'	    => 90
			)
		);

		$this->addElement(
			'hidden', 'source', array(
				'required'  => true,
				'filters'   => array('StringTrim')
			)
		);
		$this->source->setDecorators(array(
				array('Label', null),
				array('ViewHelper', null),
				array('Errors', null),
				array('Description', array('p' => 'description'))
			))
			->addValidator('Digits');


		$this->addElement(
			'text', 'tvshowEpisode_name', array(
				'required'  => false,
				'filters'   => array('StringTrim'),
				'label'	    => 'Episode',
				'size'	    => 90
			)
		);
		$this->addElement(
			'hidden', 'subSource', array(
				'required'  => false,
				'filters'   => array('StringTrim')
			)
		);
		$this->subSource->setDecorators(array(
			array('Label', null),
			array('ViewHelper', null),
			array('Errors', null),
			array('Description', array('p' => 'description'))
		))
			->addValidator('Digits');

        $typeMapper = new Application_Model_DbTable_Type();
        $types_r = $typeMapper->fetchAll();
        $types = array();
        foreach($types_r as $r) {
            $types[$r['id']] = $r['name'];
        }

        $this->addElement(
            'select', 'type', array(
                'MultiOptions'	=> $types,
                'required'	=> true,
                'label'		=> 'Type'
            )
        );

        $this->addElement(
            'text', 'typeNumber', array(
                'required'  => false,
                'filters'   => array('StringTrim'),
                'size'	    => 3
            )
        );
        $this->typeNumber->setDecorators(array(
            array('Label', null),
            array('ViewHelper', array('separator' => '<br/>', 'placement' => 'prepend')),
            array('Errors', null),
            array('Description', array('p' => 'description'))
        ));

		$this->addElement(
			'text', 'song_name', array(
				'required'  => false,
				'filters'   => array('StringTrim'),
				'label'	    => 'Song',
				'size'	    => 90
			)
		);

		$this->addElement(
			'hidden', 'song', array(
				'required'  => false,
				'filters'   => array('StringTrim')
			)
		);
		$this->song->setDecorators(array(
				array('Label', null),
				array('ViewHelper', null),
				array('Errors', null),
				array('Description', array('p' => 'description'))
			))
			->addValidator('Digits');

		$this->addElement(
			'text', 'version', array(
				'required'  => false,
				'filters'   => array('StringTrim'),
				'label'	    => 'Version',
				'size'	    => 60
			)
		);

        $this->addElement(
            'text', 'extension', array(
                'required'  => false,
                'filters'   => array('StringTrim'),
                'label'	    => 'Extension',
                'size'	    => 3
            )
        );

		$subtypeMapper = new Application_Model_DbTable_Subtype();
		$subtypes_r = $subtypeMapper->fetchAll();
		$subtypes = array();
		foreach($subtypes_r as $r) {
			$subtypes[$r['id']] = $r['name'];
		}

		$this->addElement(
			'select', 'subType', array(
				'MultiOptions'	=> $subtypes,
				'required'	=> true,
				'label'		=> 'Subs Type'
			)
		);

		$langMapper = new Application_Model_DbTable_Language();
		$langs_r = $langMapper->fetchAll();
		$langs = array();
		foreach($langs_r as $r) {
			$langs[$r['id']] = $r['name'];
		}

		$this->addElement(
			'select', 'language', array(
				'MultiOptions' => $langs,
				'required'  => true,
				'label'	    => 'Language'
			)
		);

		$userMapper = Application_Model_UserMapper::getInstance();
		$users_r = $userMapper->fetchAll();
		$users = array(0 => 'Other');
		foreach($users_r as $r) {
			$users[$r->getId()] = $r->getDisplayName();
		}

		$this->addElement(
			'select', 'madeBy', array(
				'MultiOptions' => $users,
				'required'  => false,
				'label'	    => 'Made by'
			)
		);

        $this->addElement(
            'text', 'duration', array(
                'required'  => false,
                'filters'   => array('StringTrim'),
                'label'	    => 'Duration',
                'size'	    => 3
            )
        );
        $this->duration->addValidator(new Application_Form_Validator_Duration());

		$this->addElement(
			'textarea', 'comment', array(
				'required'  => false,
				'filters'   => array('StringTrim'),
				'label'	    => 'Comment',
				'rows'	    => 5
			)
		);

		$this->addElement('submit', 'submit', array(
				'ignore'   => true,
				'label'    => 'Save'
			)
		);

    }
}