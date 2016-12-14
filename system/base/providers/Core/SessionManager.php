<?php

namespace Providers\Core;

class SessionManager {

    protected $app;

    protected $driver;
    
    public function __construct($app){

    	$this->app = $app;

    }

    public function initDriver($driver){

        $method = 'make'.ucfirst($driver).'Driver';

        if(method_exists($this, $method)){
        	 $this->driver = $this->method();
        }

    }

}

?>