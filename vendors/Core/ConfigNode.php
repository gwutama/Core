<?php

namespace Core;

/**
 * <h1>Class ConfigNode</h1>
 *
 * Basic representation of a configuration node.
 */
class ConfigNode implements \Iterator {

    /**
     * The configuration key.
     *
     * @var null
     */
    private $key;

    /**
     * The configuration value.
     *
     * @var null
     */
    private $value;

    /**
     * A ConfigNode can have children of type ConfigNode.
     * These are saved in an array.
     *
     * @var array
     */
    private $children = array();

    /**
     * The constructor sets key and value.
     *
     * @param null $key
     * @param null $value
     */
    public function __construct($key = null, $value = null) {
        $this->key = $key;
        $this->value = $value;
    }


    /**
     * Setter for key
     *
     * @param $key
     */
    public function setKey($key) {
        $this->key = $key;
    }


    /**
     * Getter for key.
     *
     * @return null
     */
    public function getKey() {
        return $this->key;
    }


    /**
     * Setter for value.
     *
     * @param $value
     */
    public function setValue($value) {
        $this->value = $value;
    }


    /**
     * Getter for value.
     *
     * @return null
     */
    public function getValue() {
        return $this->value;
    }


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