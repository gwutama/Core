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
     * Container for all registered services.
     *
     * @var array
     */
    protected $services = array();


    /**
     * The constructor sets services if an array of services
     * is passed to the constructor.
     *
     * @param array $services
     */
    public function __construct($services = array()) {
        $this->services = $services;
    }


    /**
     * Iterator rewind().
     */
    public function rewind() {
        reset($this->services);
    }


    /**
     * Iterator current().
     *
     * @return mixed
     */
    public function current() {
        return current($this->services);
    }


    /**
     * Iterator next().
     *
     * @return mixed
     */
    public function next() {
        return next($this->services);
    }


    /**
     * Iterator key().
     *
     * @return mixed
     */
    public function key() {
        return key($this->services);
    }


    /**
     * Iterator valid().
     *
     * @return bool
     */
    public function valid() {
        $key = key($this->services);
        return ($key !== null && $key !== false);
    }


    /**
     * Checks whether a service is available or registered.
     *
     * @param $name
     * @return bool
     */
    public function hasService($name) {
        return isset($this->services[$name]);
    }


    /**
     * Returns an instance of the service.
     *
     * @param $name
     * @return null
     */
    public function getService($name) {
        if($this->hasService($name)) {
            $service = $this->services[$name];
            return $service->getInstance();
        }
        throw new ServiceNotAvailableException("Service not available or not registered: ".$name.".");
    }


    /**
     * Registers a service. Alias can be used to prevent namespace problems.
     *
     * @param $name
     * @param array $options
     * @param $alias
     */
    public function register($name, $options = array(), $alias = null) {
        if($name && $alias) {
            $this->services[$alias] = new Service($name, $options);
        }
        elseif($name && !$alias) {
            $this->services[$name] = new Service($name, $options);
        }
    }


    /**
     * Clear services.
     */
    public function clear() {
        $this->services = array();
    }


    /**
     * Returns the number of registered services.
     *
     * @return int
     */
    public function count() {
        return count($this->services);
    }


    /**
     * Returns a service with an attribute call.
     *
     * @param $name
     */
    public function __get($name) {
        return $this->getService($name);
    }


    /**
     * Returns a service with a method call
     *
     * @param $name
     * @param array $args
     */
    public function __call($name, $args = array()) {
        if(preg_match("/get([\w]+)Service/", $name, $matches)) {
            return $this->getService($matches[1]);
        }
        return null;
    }
}

?>