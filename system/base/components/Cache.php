<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {Cache.php}
 *
 */

use \Providers\Services\CacheService as CacheService;

final class Cache {

   private static $instance = NULL;

   private $cache_service;

   private function __construct(array $options){

       $driver = $options['cache_driver'];

       if($driver === "memcached"){  

            $this->cache_service = new CacheService($options);
       }
   }

   public static function createInstance(array $options){

       if(static::$instance == NULL){
             static::$instance = new Cache($options);
             return static::$instance;
        }
   }

   public static function set($key, $value){

       static::$instance->cache_service->set($key, $value);
   }

   public static function get($key){

   	   static::$instance->cache_service->get($key);
   }

   public static function has($key){

      return static::$instance->cache_service->has($key);
   }

}


?>