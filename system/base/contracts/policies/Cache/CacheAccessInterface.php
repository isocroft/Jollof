<?php

namespace Contracts\Policies;

interface CacheAccessInterface {

    public function set($key, $val);

    public function get($key);

    public function has($key);

}


?>
