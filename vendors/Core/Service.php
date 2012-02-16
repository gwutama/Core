<?php

namespace Core;

class Service {

    /**
     * @var
     */
    private $instance;

    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $options;


    /**
     * @param $isSingleton
     */
    public function __construct($name, $options = array()) {
        $this->name = $name;
        $this->options = $options;
        $this->setInstance();
    }


    /**
     * Sets the object instance.
     */
    private function setInstance() {
        $class = new ReflectionClass($this->name);
        $constructor = $class->getConstructor();
        $parameters = $constructor->getParameters();
        var_export($parameters);

        $objParams = array();

        foreach($parameters as $param) {
            $name = $param->name;
            if($param->isDefaultValueAvailable() && !isset($this->options[$name])) {
                $objParams[$name] = $param->getDefaultValue();
            }
            else {
                $objParams[$name] = $this->options[$name];
            }
        }

        $this->instance = $class->newInstanceArgs($objParams);
    }


    /**
     * Returns the instance.
     *
     * @return mixed
     */
    public function getInstance() {
        return $this->instance;
    }

}

?>
