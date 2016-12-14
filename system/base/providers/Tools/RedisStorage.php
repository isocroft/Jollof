<?php

/**
 * Jollof (c) Copyright 2016
 *
 *
 * {RedisStorage.php}
 *
 */

namespace Providers\Tools;

class RedisStroage {

	 protected $client;

	 protected $host;

	 protected $port;

     public function __construct($host, $port){
     	
     		// PredisAutoloader::register();

     		try{
     		
     			$this->client = new PredisClient(array(
     				"scheme" => "tcp",
     				"host" => $host,
     				"port" => $port
     			));

     		}catch(\Exception $e){

     			$this->client = NULL;

     			throw $e;
     		}
     }

     public function get($key){

     	 return $this->client->get($key);
     }

     public function set($key, $value){

     	 $this->client->set($key, $value);
     }

     public function incr($key){

     	$this->client->incr($key);
     }

     public function decr(){

     	$this->client->decr($key);
     }

}


?>