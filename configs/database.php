<?php

use Core\Config as Config;
use Core\InvalidConfigKeyException as InvalidConfigKeyException;

Config::set("database", array(
    "production" => array(
        "dsn" => "mysql:host=localhost;dbname=production",
        "username" => "root",
        "password" => "",
        "persistent" => false
    ),
    "debug" => array(
        "dsn" => "mysql:host=localhost;dbname=debug",
        "username" => "root",
        "password" => "",
        "persistent" => false
    )
));

?>