<?php

Config::set("routes", array(
    "/foo/{bar}/{baz}/{blah}" => array(
        "controller" => "Foobar",
        "action" => "index"
    )
));

?>