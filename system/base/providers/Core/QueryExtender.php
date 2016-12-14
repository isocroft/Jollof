<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryExtender.php}
 */

namespace Providers\Core;

use \Model;

class QueryExtender {
     
     protected $queryString;

     protected $attribs;

     protected $relations;

     protected $paramValues;

     protected $havingValues;

     protected $paramTypes;

     protected $connection;

     protected $allowedConjunctions = array(
        'AND',
        'OR',
        'NOT',
        'OR NOT'
    );

    protected $allowedOperators = array(
        'LIKE',
        'IN',
        '>',
        '<>',
        '=',
        '<'
    );

     public function __construct($connection, $paramTypes){

         $this->queryString = '';

         $this->paramValues = NULL;

         $this->havingValues = NULL;

         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

     }

     
     public function get($columns, $clauseProps, $conjunction){ 
     	
        $conjunction = " ".strtoupper($conjunction) ." ";

        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= "SELECT " . $_columns . " FROM " . $table . (count($clauseProps) > 0? " <;join> WHERE " . implode($conjunction, $this->prepareSelectPlaceholder($clauseProps)) : " <;join>");

        return $this;
     }

     public function set($columns, $values){

        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= "INSERT INTO " . $table . "(" . $_columns . ") VALUES (" .  $this->prepareInsertPlaceholder($values) . ")";

        return $this;
     }

     public function let($columns, $conjunction){

        $table = $this->wrap($this->attribs['table']);

        $this->queryString .= "UPDATE " . $table . " SET " . implode($conjunction, $this->prepareUpdatePlaceholder($columns));

        return $this;
     }

     public function del($columns){

      $table = $this->wrap($this->attribs['table']);

     	$this->queryString .= "DELETE " . $columns . " FROM " . $table;

        return $this;
     }

     public function with(Model $model, $joinType = 'inner'){

         if(strlen($this->queryString) == 0){

             return $this;
         }

         $joinType = strtoupper($joinType);

         $__atrribs = $model->getAttributes();

         $table = $this->wrap($this->attribs['table']);

         $joinTable = $this->wrap($__atrribs['table']);

         $parentReference = $this->wrap($this->attribs['key']);

         $childReference = $this->wrap($__attribs['relations'][get_class($model)]);

     	 $joinExp = " $joinType JOIN $joinTable ON $table.$parentReference = $joinTable.$childReference";

         $this->queryString = str_replace(' <;join>', $joinExp, $this->queryString);

     	 return $this;
     }

     public function ordering(array $columns, $ascending = false){

        $direction = ($ascending? 'ASC' : 'DESC');
        $orders = array();

        foreach ($columns as $column) {

            $orders[] = " ORDER BY " . $this->wrap($column) . " $direction";
        }

        $this->queryString .= (implode(', ', $orders));

     	return $this;

     }

     public function setAttributes($schemaAttribs){ 

     	 $this->attribs = $schemaAttribs;
     }

     public function having(array $clauseProps){

        $this->queryString .= (" HAVING ");

        return $this;
     }

     public function grouping(array $columns){

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= (" GROUP BY " . $_columns);

        return $this;
     }

     public function exec($offset = 0, $limit = 0){

     	  $type = substr($this->queryString, 0, index_of($this->queryString, " ", 0));
     	  $result = NULL;
          $stoppers = array();

          if($offset > 0){
             $stoppers[] = " OFFSET $offset";
          }

          if($limit > 0){
             $stoppers[] = " LIMIT $limit";
          }
        
          switch (strtolower($type)){
          	case 'select':
                $this->queryString .= implode(',', $stoppers);
                $this->queryString = str_replace(' <;join>', '', $this->queryString);
                $result = db_get($this->connection, $this->paramTypes, $this->queryString, $this->parmeterizeValues($this->paramValues));
          	break;
          	case 'insert':
                 $result = db_put($this->connection, $this->paramTypes, $this->queryString, $this->parmeterizeValues($this->paramValues));
          	break;
          	case 'update':
                 $result = db_post($this->connection, $this->paramTypes, $this->queryString, $this->parmeterizeValues($this->paramValues));
          	break;
          	case 'delete':
                 $result = db_del($this->connection, $this->paramTypes, $this->queryString, $this->parmeterizeValues($this->paramValues));
          	break;
          	default:
          		# code...
          	break;
          }

          return $result;   
     }

     private function wrap($columnName, $char = "`"){

          if(strlen($columnName) === 0){
                return $columnName;
          }

          return $char.$columnName.$char;
     }

     private function prepareInsertPlaceholder($props){

     	  $this->paramValues = $props;
       
        return array_fill(0, count($props), "?");
     }

     private function prepareHavingPlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);    

        $this->havingValues = array_pluck($rawValues, 1); 

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);
         
        return array_map('update_placeholder', $sqlProps);  
     }

     private function prepareSelectPlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        $this->paramValues = array_pluck($rawValues, 1); 

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);
         
        return array_map('update_placeholder', $sqlProps);
     }

     private function prepareUpdatePlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        $this->paramValues = array_pluck($rawValues, 1); 

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);

        return array_map('update_placeholder', $sqlProps);
     }

     private function parmeterizeValues($params){
     	    $values = array();
          foreach ($params as $value) {
          	  $type = substr(gettype($value), 0, 3);
          	  $values[$type] = $this->escapeSQLTokenChars($value);
          } 
          return $values;
     }

     private function escapeSQLTokenChars($val){

         $_val = str_replace(array('%'), array('\%'), $val);
         return $_val;
     }

}

?>