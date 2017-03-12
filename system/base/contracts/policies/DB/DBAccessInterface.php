<?php

namespace Contracts\Policies;

interface DBAccessInterface {

      function getAttributes();

      function installSchema();

      function bindSchema();

}


?>
