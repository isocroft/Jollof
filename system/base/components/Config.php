<?php

/*!
 * Jollof Framework (c) 2016
 * 
 * {Config.php}
 *
 */

final class Config {
	
	/**
     * @var Auth
     */

     private static $instance = NULL;

    /**
     * Constructor.
     *
     * @param void
     *
     * @scope public
     */

     private function __construct(array $envs){


     }

     public static function createInstance(array $envs){
 
          if(static::$instance == NULL){
               static::$instance = new Config($envs);
               return static::$instance;
          }

     }
}

?>