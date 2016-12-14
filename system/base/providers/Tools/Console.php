<?php

namespace Providers\Tools;

use \Providers\Tools\ArgvInput as ArgvInput;
use \Providers\Tools\ArgvOutput as ArgvOutput;


class Console {

   private $input;

   private $output;

   public function __construct(){

       $this->input = new ArgvInput();

       $this->output = new ArgvOutput();

   }


}

?>