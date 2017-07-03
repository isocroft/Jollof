<?php


/*!
 * Jollof (c) Copyright 2016
 *
 * {Session.php}
 *
 */

use \Providers\Services\NativeSessionService as NativeService;
use \Providers\Services\RedisSessionService as RedisService;


final class Session {

     /**
      * @var Session
      */

     private static $instance = NULL;

     /**
      * @var Contracts\Policies\SessionAccessInterface
      */

     private  $session_service;

     /**
      * @var string
      */

     private $driver;

     /**
      * Constructor.
      *
      *
      * @param void
      * @api
      */

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
     * Retrieve the core session service.
     *
     *
     * @param void
     * @return string
     */

    public function getDriver(){

        return $this->session_service;
    }

    /**
     *
     *
     *
     *
     * @param void
     * @return object $instance
     * @api
     */

    public static function createInstance(array $config){

          if(static::$instance == NULL){
               static::$instance = new Session($config);
               return static::$instance;
          }
     }

     /**
      * Checks if an item exists in the session using its' 
      * {$key}
      *
      *
      * @param string $key
      * @return bool
      * @api
      */


     public static function has($key){

        return static::$instance->session_service->hasKey($key);
     }

     /**
      * 
      *
      *
      * @param string $key
      * @return mixed
      * @api
      */

     public static function get($key){

        return static::$instance->session_service->read($key);
     }

     /**
      * Inserts an item into the session using a 
      * {$key}
      *
      *
      * @param string $key
      * @param mixed $value
      * @return bool
      * @api
      */

     public static function put($key, $value){

        return static::$instance->session_service->write($key, $value);
     }

     /**
      * Deletes an item from the session using its' 
      * {$key}
      *
      *
      * @param string $key
      * @return bool
      * @api
      */


     public static function forget($key){

        return static::$instance->session_service->erase($key);
     }

     /**
      * Destroys the entire session data and cookie. 
      *
      *
      *
      * @param void
      * @return bool
      * @api
      */

     public static function drop(){

         return static::$instance->session_service->destroy(static::$instance->session_service->getName());
     }

     /**
      * Retrieves the session id. 
      *
      *
      *
      * @param void
      * @return string
      * @api
      */

     public static function id(){

        return static::$instance->session_service->getId();

     }

     /**
      * Checks if session data and cookie has been properly 
      * destroyed
      *
      *
      * @param void
      * @return mixed
      * @api
      */

     public static function hasDropped(){

         $name = static::$instance->session_service->getName();

         return (!array_key_exists($name, $_COOKIE));
     }

     /**
      * Retrieves the session CSRF token 
      *
      *
      *
      * @param void
      * @return mixed
      * @api
      */

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