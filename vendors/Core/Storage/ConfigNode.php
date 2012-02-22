<?php

namespace Core\Storage;

/**
 * <h1>Class ConfigNode</h1>
 *
 * Basic representation of a configuration node.
 */
class ConfigNode extends StorageNode implements \Iterator {

    /**
     * A ConfigNode can have children of type ConfigNode.
     * These are saved in an array.
     *
     * @var array
     */
    private $children = array();


    /**
     * Setter for children.
     *
     * @param array $children
     */
    public function setChildren($children = array()) {
        $this->children = $children;
    }


    /**
     * Returns the children.
     *
     * @return array
     */
    public function &getChildren() {
        return $this->children;
    }


    /**
     * Returns a child if exists. Null if inexists.
     *
     * @param $key
     * @return null
     */
    public function &getChild($key) {
        if(isset($this->children[$key])) {
            return $this->children[$key];
        }
        return null;
    }


    /**
     * Iterator rewind().
     */
    public function rewind() {
        reset($this->children);
    }


    /**
     * Iterator current().
     *
     * @return mixed
     */
    public function current() {
        return current($this->children);
    }


    /**
     * Iterator next().
     *
     * @return mixed
     */
    public function next() {
        return next($this->children);
    }


    /**
     * Iterator key().
     *
     * @return mixed
     */
    public function key() {
        return key($this->children);
    }


    /**
     * Iterator valid().
     *
     * @return bool
     */
    public function valid() {
        $key = key($this->children);
        return ($key !== null && $key !== false);
    }


    /**
     * Returns the number of children.
     *
     * @return int
     */
    public function count() {
        return count($this->children);
    }
}

?>