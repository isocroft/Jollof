<?php

namespace Contracts\Policies;

interface QueryProvider {
    

     public function get(\array $columns, \array $clauseProps, $conjunction);

     public function set(\array $columns, \array $values, \array $clauseProps);

     public function let(\array $columnValues, \array $clauseProps, $conjunction);

     public function del(\array $columns, \array $clauseProps);

     public function setAttributes(\array $schemaAttribs);

     public function exec($limit, $offset);

}

?>