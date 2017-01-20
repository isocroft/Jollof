<?php

/*!
 * Jollof Framework (c) 2016
 *
 * {CacheSerivce.php}
 */

namespace Providers\Services;

use \Contracts\Policies\CacheAccessInterface as CacheAccessInterface;

class CacheService implements CacheAccessInterface {


      protected $memcache;

      protected $isCacheAvialable;

      public function __construct($options){

         try{

      	     $this->memcache = new Memcache;

      	     $this->isCacheAvialable = $this->memcache->connect($options['host'], $options['port']);

         }catch(\Exception $e){

              throw $e;
         }
      }

      public function set($key, $val){

           $this->memcache->set($key, $val);
      }

      public function get($key){

           return $this->memcache->get($key);
      }

      public function has($key){


      }

}

?>
