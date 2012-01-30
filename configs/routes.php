<?php

use Core\Config as Config;
use Core\InvalidConfigKeyException as InvalidConfigKeyException;

Config::set("routes", array(
    "/foo/{bar}/{baz}/{blah}" => array(
        "controller" => "Foobar",
        "action" => "index"
    )
));

?>