<?php

use \Providers\Services\NativeSessionService as NativeService;
use \Providers\Services\RedisSessionService as RedisService;

final class Session {

     private static $instance = NULL;

     private  $session_service;

     private $driver;

     private function __construct(array $config){

        // @TODO: use {SessioManager} class in the next code update to do the below more efficiently...
        $this->driver = $config['session_driver'];

        if($this->driver === '#native'){

             $this->session_service = new NativeService($config['session_name']);

        }else if($this->driver === '#redis'){

             $this->session_service = new RedisService($config['session_name'], $config['sessions_host'], $config['sessions_port']);
        }else{

            ; // I really don't know...
        }
     }

    /**
     *
     *
     * @param void
     * @return string
     */

    public function getDriver(){

        return $this->session_service;
    }


     public static function createInstance(array $config){

          if(static::$instance == NULL){
               static::$instance = new Session($config);
               return static::$instance;
          }
     }

     public static function has($key){

        return static::$instance->session_service->hasKey($key);
     }

     public static function setCookieValue($cookies){

          if(static::$instance->driver !== "#redis"){

               return FALSE;
          }
     }

     public static function get($key){

        return static::$instance->session_service->read($key);
     }

     public static function put($key, $value){

        return static::$instance->session_service->write($key, $value);
     }

     public static function forget($key){

        return static::$instance->session_service->erase($key);
     }

     public static function drop(){

         return static::$instance->session_service->destroy(static::$instance->session_service->getName());
     }

     public static function id(){

        return static::$instance->session_service->getId();
     }

     public static function hasDropped(){

         $name = static::$instance->session_service->getName();

         return (!array_key_exists($name, $_COOKIE));
     }

     public static function token(){

     	$_token;

        if(static::$instance->session_service->hasKey('_token')){

            $_token =  static::$instance->session_service->read('_token');
        }else{

            $_token = get_random_as_range(TRUE);

            static::$instance->session_service->write('_token',  $_token);
        }

        return $_token;
     }


}

?>