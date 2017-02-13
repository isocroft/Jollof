<?php

/*!
 * Jollof Framework (c) 2016
 *
 *
 * {LoginThrottle.php}
 */

namespace Providers\Tools;

use \UserThrottle;

class LoginThrottle {

	  protected $throttleClass;

	  protected $throttleId;

      public function __construct($throttle, $throtId){

      		$this->throttleClass = $throttle;
      		$this->throttleId = $throtId;
      }

      /**
       * Handle dynamic method calls into the Model static method.
       *
       * @param  string  $method
       * @param  array   $parameters
       * @return mixed
       * @throws \BadMethodCallException
       */

      public function __call($method, $parameters = array()){
        
        	$args = $parameters;

        	try{

	      		if(PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION <= 2){ // < 5.2.*

	      			return call_user_func(array($this->throttleClass, $method), $args);

	      		}else{ // > 5.2

	      			return call_user_func($this->throttleClass.'::'.$method, $args);
	      		}

	      	}catch(\Exception $e){	

        		throw new \BadMethodCallException("Method [$method] is not supported by Sentry or no User has been set on Sentry to access shortcut method.");
        	}	
      }

      /**
   	   * 
       *
       *
       * @param array $colVals
       * @param array $clause
       * @return void
       */

      public function ban(array $colVals, array $clause){

      			return $this->update($clause, $colVals);

      }

      /**
   	   * 
       *
       *
       * @param void
       * @return void
       */

      public function isUserBanned(){

      		return FALSE;
      }


      /**
   	   * 
       *
       *
       * @param void
       * @return bool
       */

      public function isAttemptLimitReached(){

      }

      /**
   	   * 
       *
       *
       * @param void
       * @return bool
       */

      public function setAttempt(array $props, array $clause){

      			$this->createOrUpdate($props, $clause);

      }


      /**
   	   * 
       *
       *
       *
       * @param array $sessioData
       * @return 
       */

      public function updateSessionDataStore(array &$sessionData){

      		$throttleData = $this->fetchById($this->throttleId);

      }

      public function setThrottleId($id){

      		$this->throttleId = $id;
      }

}

?>