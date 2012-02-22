<?php

namespace Core\Storage;

/**
 * <h1>Class BaseStorageNode</h1>
 *
 * Basic representation of a storage node.
 */
abstract class StorageNode {

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

}

?>