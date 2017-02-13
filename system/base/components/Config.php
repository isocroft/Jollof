<?php

/*!
 * Jollof Framework (c) 2016
 *
 *
 * {Config.php}
 */

final class Config {

	/**
      * @var Config
      */

     private static $instance = NULL;

     /**
      * @var array
      */

     private $configBlock;

     /**
      * 
      *
      *
      * @param stirng $key
      * @return mixed
      * @api
      */


     /**
      * Constructor.
      *
      *
      * @param void
      * @api
      */

     private function __construct(array $cblock){

          $this->configBlock = $cblock;

     }


      /**
       *
       *
       *
       *
       *
       * @param void
       * @return object $instance
       * @api
       */

     public static function createInstance(array $cblock){

          if(static::$instance == NULL){
               static::$instance = new Config($cblock);
               return static::$instance;
          }

     }
}

?>