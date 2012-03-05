<?php

namespace Core\Storage;
use Core\Utility\Node;

/**
 * <h1>Class ConfigNode</h1>
 *
 * Basic representation of a configuration node.
 * A ConfigNode can have children of type ConfigNode.
 */
class ConfigNode extends Node {

    /**
     * Sets a child into node.
     * @param \Core\Utility\Node $object
     * @param null $key
     */
    public function setChild(Node $object, $key = null) {
        parent::setChild($object, $key);
    }

}

?>