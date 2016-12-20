<?php

namespace Contracts\Policies;

interface DBAccessInterface {

      public function get(\array $columns, \array $clauseProps, $conjunction);

      public function set(\array $values, \array $clauseProps);

      public function let(\array $columnValues, \array $clauseProps, $conjunction);

      public function del(\array $columns);

      public function rawGet($query, \array $columns, \array $clauseProps);

}


?>
