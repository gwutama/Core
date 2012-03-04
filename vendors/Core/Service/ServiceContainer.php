<?php

namespace Core\Service;

use Core\Utility\ObjectCollection;
use Core\ServiceNotAvailableException;

/**
 * <h1>Class ServiceContainer</h1>
 *
 * <p>
 * This class represents a dependency container of the dependency injection pattern.
 * </p>
 */
class ServiceContainer extends ObjectCollection {

    /**
     * Checks whether a service is available or registered.
     *
     * @param $name
     * @return bool
     */
    public function hasService($name) {
        return $this->hasObject($name);
    }


    /**
     * Returns an instance of the service.
     *
     * @param $name
     * @return null
     */
    public function getService($name) {
        if($this->hasObject($name)) {
            $service = $this->objects[$name];
            return $service->getInstance();
        }
        throw new ServiceNotAvailableException("Service not available or not registered: ".$name.".");
    }


    /**
     * Registers a service. Alias can be used to prevent namespace problems.
     *
     * @param $name
     * @param array $options
     * @param array $callbacks
     * @param $alias
     */
    public function register($name, $options = array(), $callbacks = array(), $alias = null) {
        if($name && $alias) {
            $this->objects[$alias] = new Service($name, $options, $callbacks);
        }
        elseif($name && !$alias) {
            $this->objects[$name] = new Service($name, $options, $callbacks);
        }
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