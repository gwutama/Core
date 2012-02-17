<?php

namespace Core;

/**
 * <h1>Class ServiceContainer</h1>
 *
 * <p>
 * This class represents a dependency container of the dependency injection pattern.
 * </p>
 */
class ServiceContainer implements \Iterator {

    /**
     * @var array
     */
    protected $services = array();


    /**
     * @param array $services
     */
    public function __construct($services = array()) {
        $this->services = $services;
    }


    /**
     *
     */
    public function rewind() {
        reset($this->services);
    }


    /**
     * @return mixed
     */
    public function current() {
        return current($this->services);
    }


    /**
     * @return mixed
     */
    public function next() {
        return next($this->services);
    }


    /**
     * @return mixed
     */
    public function key() {
        return key($this->services);
    }


    /**
     * @return bool
     */
    public function valid() {
        $key = key($this->services);
        return ($key !== null && $key !== false);
    }


    /**
     * @param $name
     * @return bool
     */
    public function hasService($name) {
        return isset($this->services[$name]);
    }


    /**
     * @param $name
     * @return null
     */
    public function getService($name) {
        if($this->hasService($name)) {
            $service = $this->services[$name];
            return $service->getInstance();
        }
        return null;
    }


    /**
     * @param $name
     * @param array $options
     */
    public function register($name, $options = array()) {
        $this->services[$name] = new Service($name, $options);
    }


    /**
     * @param $name
     * @param array $args
     */
    public function __call($name, $args = array()) {
        if($match = preg_match("/get([\w]+)Service/", $name)) {
            return $this->getService($match[1]);
        }
        return null;
    }


    /**
     * @param $service
     */
    public function __get($service) {
        return $this->getService($service);
    }
}

?>