<?php

class Application_Form_Validator_Duration extends Zend_Validate_Abstract {
    const DURATION = 'duration';

    protected $_messageTemplates = array(
	self::DURATION => "'%value%' is not a valid duration"
    );

    public function isValid($value) {
	$this->_setValue($value);

	if (!is_numeric($value)) {
	    if (preg_match('/^[0-9]+(\.|:)[0-9]+$/', $value) != 1) {
		$this->_errors();
		return false;
	    }
	}

	return true;
    }

}