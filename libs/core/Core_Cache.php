<?php

abstract class Core_Cache {
}

/*

if(Cache::isExpired($key)) {
    $data = ...;
    Cache::write($key, $data, Config::get(cache.long));
}
else {
    $data = Cache::read($key);
}

*/

?>