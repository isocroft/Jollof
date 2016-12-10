<?php

namespace Contracts\Policies;

interface SessionAccessInterface {

     public function open();

     public function close();

     public function getId();

     public function getName();

     public function destroy($name);    

     public function read($key);

     public function write($key, $val);


}

?>
