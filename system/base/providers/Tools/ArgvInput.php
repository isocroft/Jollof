<?php

namespace Providers\Tools;

class ArgvInput {

  private $options;

  private $command;

  private $arguments;

  private $command_setup;
  

  public function __construct($argv = NULL, $csetup = array()){

     $this->options = array("long" => array(), "short" => array());

     $this->arguments = array();

     if($argv == NULL){

        if(array_key_exists('argv', $_SERVER))
              $this->command = $_SERVER['argv'];
         else  
              $this->command = NULL;
     } 

     $this->command_setup = $csetup;
  }


  public  function __destruct(){

  }

 public function __clone(){


 }

  private function parseArgvAsLongOption(){


  }

  private function parseArgvAsShortOption(){


  }

  private function parseArgvAsArgument(){

 
  }

  public function __destruct(){

  }

  public function getCommand(){


  }

  public function setCommand(){


  }

  public function getCommandSetup(){


  }


}

?>
