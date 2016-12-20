<?php

namespace Providers\Tools;

use \Providers\Tools\ArgvInput as ArgvInput;
use \Providers\Tools\ArgcInput as ArgcInput;
use \Providers\Tools\Prompt as Prompt;


class Console {

   private $argv;

   private $argc;

   public function __construct($script_root){

       $this->argv = new ArgvInput();
      
       $this->argc = new ArgcInput();
      
        $this->prompt = new Prompt($this->argv, $this->argc);


   }


}

?>
