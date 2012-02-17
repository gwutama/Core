<?php

namespace Core;

/**
 * <h1>Class Service</h1>
 *
 * <p>
 * This class represents a service of a dependency injection pattern.
 * </p>
 */
class Service {

    /**
     * For singleton object.
     *
     * @var
     */
    private static $instance;

    /**
     * Service name.
     *
     * @var
     */
    private $name;

    /**
     * Options.
     *
     * @var
     */
    private $options;


    /**
     * Whether this service is singleton or not.
     *
     * @var bool
     */
    private $isSingleton = false;


    /**
     * The consturctor sets the service name, options
     * and whether this service implements singleton pattern.
     *
     * @param $name
     * @param array $options
     */
    public function __construct($name, $options = array()) {
        $this->name = $name;
        $this->options = $options;

        if(isset($options["isSingleton"]) && $options["isSingleton"] == true) {
            $this->isSingleton = true;
        }
    }


    /**
     * Creates a new object.
     *
     * @return object
     */
    private function createInstance() {
        $class = new \ReflectionClass($this->name);
        $constructor = $class->getConstructor();
        $objParams = array();

        // Build constructor parameters
        if($constructor) {
            $parameters = $constructor->getParameters();
            foreach($parameters as $param) {
                $name = $param->name;
                if($param->isDefaultValueAvailable() && !isset($this->options[$name])) {
                    $objParams[$name] = $param->getDefaultValue();
                }
                else {
                    $objParams[$name] = $this->options[$name];
                }
            }
        }

        return $class->newInstanceArgs($objParams);
    }


    /**
     * Gets the object instance.
     */
    public function getInstance() {
        if($this->isSingleton == true) {
            if(self::$instance == null) {
                self::$instance = $this->createInstance();
            }
            return self::$instance;
        }

        try {
            return $this->createInstance();
        }
        catch(\ReflectionException $e) {
            throw new CannotCreateServiceException("Error creating instance: ".$this->name.". Class exists?");
        }
    }


    /**
     * Returns the service name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }
}

?>