<?php

namespace Providers\Tools;

class AuthContext {

	  private $models;

      public function __construct(array $auth_models){

      		$this->models = $auth_models;
      }

      public function create(){


      }

      public function getThrottle(){

      }

      public function getUser(){

      }

      public function getPermissions(){

      }

}

?>