<?php

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Component {
    public $component;

    public function __construct($file) {
        $this->component = $file;
    }
}

