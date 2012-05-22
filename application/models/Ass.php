<?php

class Application_Model_Ass extends Application_Model_Abstract {
    protected $_styles = array();
    protected $_dialogues = array();

    public function loadFile($filename) {
	$lines = file($filename);
	foreach($lines as $line) {
	    if (strstr($line, ':')) {
		list($lineType, $params) = explode(':', $line);
		if ($lineType == 'Style') {
		    $this->_styles[] = explode(',', $params);
		} else if ($lineType == 'Dialogue') {
		    $this->_dialogues[] = explode(',', $params);
		}
	    }
	}
    }

    public function getFonts() {
	$fonts = array();
	foreach($this->_styles as $style) {
	    $fonts[$style[0]] = $style[1];
	}

	return $fonts;
    }
}