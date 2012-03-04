<?php

namespace Core\Utility;

abstract class ObjectCollection implements \Iterator {

    /**
     * Container for all objects.
     *
     * @var array
     */
    protected $objects = array();


    /**
     * The constructor sets services if an array of objects
     * is passed to the constructor.
     *
     * @param array $objects
     */
    public function __construct($objects = array()) {
        $this->objects = $objects;
    }


    /**
     * Iterator rewind().
     */
    public function rewind() {
        reset($this->objects);
    }


    /**
     * Iterator current().
     *
     * @return mixed
     */
    public function current() {
        return current($this->objects);
    }


    /**
     * Iterator next().
     *
     * @return mixed
     */
    public function next() {
        return next($this->objects);
    }


    /**
     * Iterator key().
     *
     * @return mixed
     */
    public function key() {
        return key($this->objects);
    }


    /**
     * Iterator valid().
     *
     * @return bool
     */
    public function valid() {
        $key = key($this->objects);
        return ($key !== null && $key !== false);
    }


    /**
     * Checks whether an object is available or registered.
     *
     * @param $key
     * @return bool
     */
    public function hasObject($key) {
        return isset($this->objects[$key]);
    }


    /**
     * Returns an instance of the object.
     *
     * @param $key
     * @return null
     */
    public function getObject($key) {
        if($this->hasObject($key)) {
            return $this->objects[$key];
        }
        return null;
    }


    /**
     * Sets an object into collection.
     */
    public function setObject($object, $key = null) {
        if($key) {
            $this->objects[$key] = $object;
        }
        else {
            $this->objects[] = $object;
        }
    }


    /**
     * Clear objects.
     */
    public function clear() {
        $this->objects = array();
    }


    /**
     * Returns the number of registered objects.
     *
     * @return int
     */
    public function count() {
        return count($this->objects);
    }

}

?>