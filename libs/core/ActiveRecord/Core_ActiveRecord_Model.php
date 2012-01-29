<?php

define("MODEL_FIND_BY_ID", "id");
define("MODEL_FIND_ALL", "all");
define("MODEL_FIND_FIRST", "first");
define("MODEL_FIND_LAST", "last");
define("MODEL_FIND_ONE", "one");


/**
 * <h1>Class Core_ActiveRecord_Model</h1>
 *
 * <p>
 * This class represents a model part in MVC approach. It uses the Fowler's Active Record pattern,
 * or at least mimics it.
 * </p>
 */
abstract class Core_ActiveRecord_Model {

    /**
     * Database object. PDO or PDO compliant.
     */
    protected $dbo;


    /**
     * Driver name. Will be loaded along with constructor.
     */
    protected $driver;


    /**
     * Query values are saved in an array. Can be array of model objects if findAll is called.
     */
    protected $data;


    /**
     * Loads the driver DBO object.
     */
    private function __construct() {
        $obj = "ActiveRecord_Driver_".$this->driver;
        $this->db = $obj::instance(); // DBO driver implements singleton pattern.
    }


    /**
     * Returns the model class name.
     *
     * @static
     * @return string   Model object.
     */
    protected static function instance() {
        // Retrieves by primary key id
        $obj = get_class(self);
        return new $obj;
    }


    /**
     * Retrieves result.
     *
     * @static
     * @param $pos      A string either all/first/last/one/primary key id.
     * @param $options  An array of options.
     */
    public static function find($pos, $options = array()) {
        if(is_int($pos)) {
            return self::instance()->findById($pos, $options);
        }
        elseif($pos == MODEL_FIND_ALL) {
            return self::instance()->findAll($options);
        }
        elseif($pos == MODEL_FIND_FIRST) {
            return self::instance()->findFirst($options);
        }
        elseif($pos == MODEL_FIND_LAST) {
            return self::instance()->findLast($options);
        }
        elseif($pos == MODEL_FIND_ONE) {
            return self::instance()->findOne($options);
        }

        throw new InvalidFinderMethodException($pos);
    }


    /**
     * Returns all objects in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Core_ActiveRecord_Model object
     */
    public static function all($options = array()) {
        return self::instance()->findAll($options);
    }


    /**
     * Returns the first object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Core_ActiveRecord_Model object
     */
    public static function first($options = array()) {
        return self::instance()->findFirst($options);
    }


    /**
     * Returns the last object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Core_ActiveRecord_Model object
     */
    public static function last($options = array()) {
        return self::instance()->findLast($options);
    }


    /**
     * Returns an object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Core_ActiveRecord_Model object
     */
    public static function one($options = array()) {
        return self::instance()->findOne($options);
    }


    /**
     * Retrieves an object from model by primary key.
     *
     * @param $pos
     * @param array $options
     */
    public function findById($pos, $options = array()) {
        if(is_array($this->data)) {
            throw new CannotChainModelFindersException(
                "Cannot chain object with this finder: \"" .MODEL_FIND_BY_ID . "\".");
        }

        $this->data = $this->dbo->findById($pos, $options);
        return $this;
    }


    /**
     * Retrieves all objects from model.
     *
     * @param array $options
     */
    public function findAll($options = array()) {
        if(is_array($this->data)) {
            throw new CannotChainModelFindersException(
                "Cannot chain object with this finder: \"" .MODEL_FIND_ALL . "\".");
        }

        $this->data = $this->dbo->findAll($options); // returns array of model objects
        return $this;
    }


    /**
     * Retrieves first object from model.
     *
     * @param array $options
     */
    public function findFirst($options = array()) {
        if(is_array($this->data)) {
            throw new CannotChainModelFindersException(
                "Cannot chain object with this finder: \"" .MODEL_FIND_FIRST . "\".");
        }

        $this->data = $this->dbo->findFirst($options);
        return $this;
    }


    /**
     * Retrieves last object from model.
     *
     * @param array $options
     */
    public function findLast($options = array()) {
        if(is_array($this->data)) {
            throw new CannotChainModelFindersException(
                "Cannot chain object with this finder: \"" .MODEL_FIND_LAST . "\".");
        }

        $this->data = $this->dbo->findLast($options);
        return $this;
    }


    /**
     * Retrieves an object from model.
     *
     * @param array $options
     */
    public function findOne($options = array()) {
        if(is_array($this->data)) {
            throw new CannotChainModelFindersException(
                "Cannot chain object with this finder: \"" .MODEL_FIND_ONE . "\".");
        }

        $this->data = $this->dbo->findOne($options);
        return $this;
    }


    /**
     * Saves an object.
     */
    public function save() {
        if( is_array($this->data) == false ) {
            throw new ModelSaveException("Cannot save model. Invalid object model.");
        }

        try {
            $this->dbo->save($this->data);
        }
        catch(ModelDriverException $e) {
            throw new ModelSaveException($e->getMessage());
        }
    }


    /**
     * Deletes an object.
     */
    public function delete() {
        if( is_array($this->data) == false ) {
            throw new ModelSaveException("Cannot delete model. Invalid object model.");
        }

        try {
            $this->dbo->delete($this->data);
        }
        catch(ModelDriverException $e) {
            throw new ModelDeleteException($e->getMessage());
        }
    }


    /**
     * Magic method to retrieve query result by key.
     *
     * @param $key      Object key.
     */
    public function __get($key) {
        return $this->data[$key];
    }


    /**
     * Magic method to set object member variable by key.
     *
     * @param $key      Object key.
     * @param $value    Object value.
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
}

?>
