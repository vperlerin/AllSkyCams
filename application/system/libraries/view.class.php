<?php

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class View {
    public $template;

    public function __construct($file) {
        $this->template = $file;

	return $this;
    }
}
