<?php

namespace Models;
use Core\ActiveRecord\Operator\MySQL as Op;

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Model.php';

class Mock extends \Core\ActiveRecord\Model {

    protected $hasOne = array("Single");

}

?>