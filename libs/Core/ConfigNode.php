<?php

namespace Core;

/**
 * <h1>Class ConfigNode</h1>
 *
 * Basic representation of a configuration node.
 */
class ConfigNode {
    public $key;
    public $value;
    public $children = array();

    public function __construct($key = null, $value = null) {
        $this->key = $key;
        $this->value = $value;
    }
}

?>