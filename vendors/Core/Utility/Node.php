<?php

namespace Core\Utility;

/**
 * <h1>Class Node</h1>
 *
 * Basic representation of a node.
 */
class Node extends ObjectCollection {

    /**
     * The storage key.
     *
     * @var null
     */
    protected $key;

    /**
     * The storage node value.
     *
     * @var null
     */
    protected $value;


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
     * Sets a child into node.
     * @param \Core\Utility\Node $object
     * @param null $key
     */
    public function setChild(Node $object, $key = null) {
        $this->setObject($object, $key);
    }


    /**
     * Returns a child if exists. Null if inexists.
     *
     * @param $key
     * @return null
     */
    public function &getChild($key) {
        return $this->getObject($key);
    }


    /**
     * Setter for children.
     *
     * @param array $children
     */
    public function setChildren($children = array()) {
        $this->objects = $children;
    }


    /**
     * Returns the children.
     *
     * @return array
     */
    public function &getChildren() {
        return $this->objects;
    }

}

?>