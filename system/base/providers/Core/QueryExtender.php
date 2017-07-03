<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryExtender.php}
 *
 */

namespace Providers\Core;

use \Contracts\Policies\QueryProvider as QueryProvider;
// use \PDO;
use \UnexpectedValueException;
use \Exception;
use \ReflectionClass;

/* This query provider is for SQL databases */

class QueryExtender implements QueryProvider {

     /**
      * @var string - the actual SQL query string being built out
      */

     protected $queryString;

     /**
      * @var array - attributes of the base model [primary_key, table_name, relations]
      */

     protected $attribs;

     /**
      * @var array - param values to be bound to PDO prepared statement
      */

     protected $paramValues;

     /**
      * @var array - param types which are need by a PDO prepared statement [int,str]
      */

     protected $paramTypes;

     /**
      * @var \PDO - database connection object
      */

     protected $connection;

     /**
      * @var string - Model calss with relation
      */

     protected $ownerModel;

     /**
      * @var array - all join types which are allowed in a query
      */

     protected $allowedJoinTypes = array(
        'INNER',
        'RIGHT OUTER',
        'RIGHT',
        'LEFT OUTER',
        'LEFT',
        'FULL OUTER',
        'CROSS'
     );

     /**
      * @var array - all conjunctions which are allowed in a query
      */

     protected $allowedConjunctions = array(
        'AND',
        'OR',
        'NOT',
        'OR NOT'
     );

     /**
      * @var array - all operators which are allowed in the {WHERE} clause of a query
      */

    protected $allowedOperators = array(
        'LIKE',
        'NOT LIKE',
        'BETWEEN',
        'IN',
        '>',
        '<>',
        '=',
        '<'
    );

    /**
     * @var ReflectionClass - for obtaining the object of a class with just the class name
     */ 

     protected $reflClass;

    /**
     * Constructor
     *
     *
     * @param PDO $connection - PDO connection object
     * @param array $paramTypes - PDO parameter type constants
     * @api public
     */

     public function __construct($connection, array $paramTypes, $modelName = NULL){

         $this->queryString = '';

         $this->paramValues = NULL;

         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

         $this->reflClass = NULL;

         $this->ownerModel = $modelName;

     }

     /**
      * Builds out a select query
      *
      *
      * @param array $columns - columns for SELECT query
      * @param array $clauseProps - column/value pairs for WHERE clause in SELECT query
      * @param string $conjunction - conjunction for WHERE clause
      * @return \Providers\Core\QueryExtender -
      *
      * @throws UnexpectedValueException
      */

     public function get($columns, $clauseProps, $conjunction){

        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " " . strtoupper($conjunction) . " ";

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        // @TODO: this line is still important --- $_columns = implode(', ', array_map(array(&$this, 'addTablePrefix'), $columns));

        if(count($columns) == 1 && $columns[0] == '*'){
            $_columns = $columns[0];
        }else{
            $_columns = implode(', ', $columns);
        }    

        foreach($clauseProps as $key => $value){
            if(is_array($value)){
                if(count($value) > 0){
                    $keys = array_keys($value);
                    # check that WHERE clause operator supplied is allowed !!
                    if(!(in_array(strtoupper($keys[0]), $this->allowedOperators))){
                        throw new UnexpectedValueException();
                    }
                }
            }
        }

        # start building query string -> SELECT
        $this->queryString .= "SELECT <;distinct> " . $_columns . " FROM " . $table . (count($clauseProps) > 0? " <;join> WHERE " . implode($conjunction, $this->prepareSelectPlaceholder($clauseProps)) : " <;join>");

        return $this;
     }

     /**
      * Filters out distinct values in a SELECT query
      *
      *
      * 
      * 
      * @param 
      * @return \Providers\Core\QueryExtender -
      */

     public function distinct(){

          if(starts_with($this->queryString, 'SELECT')){

              $this->queryString = str_replace(' <;distinct>', ' DISTINCT', $this->queryString);
          
          }    
          return $this;
     }

     /**
      * Builds out a create table query
      *
      *
      * 
      * 
      * @param ----
      * @return \Providers\Core\QueryExtender -
      */

     public function table(){

          return $this;
     }

     /**
      * Builds out an insert query
      *
      *
      * @param array $columns - columns for INSERT query
      * @param array $values -  values for the columns for INSERT query
      * @param array $clauseProps - columns for update where duplicate key exists
      * @return \Providers\Core\QueryExtender -
      */

     public function set(array $columns, array $values, array $clauseProps = array()){

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        if(count($columns) == 1 && $columns[0] == '*'){
            $_columns = $columns[0];
        }else{
            $_columns = implode(', ', array_map(array(&$this, 'addTablePrefix'), $columns));
        }

        # start building query string -> INSERT
        $this->queryString .= "INSERT INTO " . $table . "(" . $_columns . ") VALUES (" .  implode(', ', $this->prepareInsertPlaceholder($values)) . ")";

        if(count($clauseProps) > 0){
            $this->queryString .= " ON DUPLICATE KEY UPDATE ";
            $this->queryString .= implode(', ', $this->prepareUpdatePlaceholder($clauseProps));
        }

        return $this;
     }

     /**
      * Builds out an update query
      *
      *
      * @param array $columnValues - column/value pairs for SET clause in UPDATE query
      * @param array $clauseProps - column/value pairs for WHERE clause in UPDATE query
      * @param string $conjunction - conjunction for WHERE clause
      * @return \Providers\Core\QueryExtender -
      *
      * @throws UnexpectedValueException
      */

     public function let($columnValues, $clauseProps, $conjunction){

        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " " . strtoupper($conjunction) . " ";

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        # start building query string -> UPDATE
        $this->queryString .= ("UPDATE " . $table . " SET " . (implode(', ', $this->prepareUpdatePlaceholder($columnValues))));

        if(count($clauseProps) > 0){
            $this->queryString .= " WHERE ". implode($conjunction, $this->prepareUpdatePlaceholder($clauseProps));
        }

        return $this;
     }


     /**
      * Builds out an delete query
      *
      *
      *
      * @param $columns - columns
      * @param $clauseProps - column/value pairs for WHERE clause in DELETE query
      * @return \Providers\Core\QueryExtender -
      */

     public function del(array $columns, array $clauseProps){

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        if(count($columns) == 1 && $columns[0] == '*'){
            $_columns = $columns[0];
        }else{
            $_columns = implode(', ', array_map(array(&$this, 'addTablePrefix'), $columns));
        }

        # start building query string -> DELETE
     	  $this->queryString .= "DELETE " . $columns . " FROM " . $table;

        if(count($clauseProps) > 0){
              ;
        }

        return $this;
     }

     /**
      *
      *
      *
      *
      * @param string $modelName -
      * @param string $joinType -
      * @return \Providers\Core\QueryExtender -
      * @throws Exception, UnxepectedValueException
      */

     public function with($modelName, $joinType = 'inner'){

         if(strlen($this->queryString) == 0){

             return $this;
         }

         $joinType = strtoupper($joinType);

         $this->reflClass = new ReflectionClass($modelName);

         $object = $this->reflClass->newInstanceWithoutConstructor(); 

         $__attribs = $object->getAttributes();

         # wrap the table name with quotes like so: `table`
         $table = $this->wrap($this->attribs['table']);

         $joinTable = $this->wrap($__attribs['table']);

         $parentReference = $this->wrap($this->attribs['key']);

         $relations = $__attribs['relations'];

         $parentModelName = $this->ownerModel;

         if(!array_key_exists($parentModelName, $relations)){

              throw new Exception("No relations exists between Model -> {$parentModelName} and Model -> {$modelName}");
         }

         $relation = $relations[$parentModelName];

         $childReference = $this->wrap((index_of($relation, '@'))? substr($relation, 1) : NULL);

         /* if the join type isn't valid, throw an error */

         if(!in_array($joinType, $this->allowedJoinTypes)){

              throw new UnexpectedValueException("Invalid join type found");
         }

         if(is_null($childReference)){

              throw new UnexpectedValueException("Invalid join reference found");
         }

         # start building query string -> JOIN
     	   $joinExp = " {$joinType} JOIN {$joinTable} ON {$table}.{$parentReference} = {$joinTable}.{$childReference}";

         // $this->queryString = str_replace(" FROM {$table}", " FROM {$table}, {$joinTable}", $this->queryString);

         $this->queryString = str_replace(' <;join>', $joinExp, $this->queryString);

     	   return $this;
     }

     /**
      *
      *
      *
      *
      * @param array $columns - ordering columns for SELECT query
      * @param bool $ascending - flag to specify sorting preference
      * @return \Providers\Core\QueryExtender -
      */

     public function ordering(array $columns, $ascending = FALSE){

        $direction = ($ascending? 'ASC' : 'DESC');
        $columnOrder = array();

        foreach ($columns as $column) {

            $columnOrder[] = " ORDER BY " . $this->wrap($column) . " $direction";
        }

        $this->queryString .= (implode(', ', $columnOrder));

     	  return $this;

     }

     public function setAttributes(array $schemaAttribs){

     	  $this->attribs = $schemaAttribs;
     }

     /**
      * Builds out the HAVING clause to a SELECT query
      *
      *
      *
      * @param array $clauseProps -
      * @return \Providers\Core\QueryExtender -
      * @throws \UnexpectedvalueException -
      */

     public function having(array $clauseProps){

        foreach ($clauseProps as $key => $value) {
            if(is_array($value)){
                if(count($value) > 0){
                    # check that HAVING clause operator supplied is allowed !!
                    if(!(in_array(strtoupper($value[0]), $this->allowedOperators))){
                        throw new UnexpectedValueException();
                    }
                }
            }
        }

        $this->queryString .= (" HAVING " . implode(', ', $this->prepareSelectPlaceholder($clauseProps)));

        return $this;
     }

     /**
      *
      *
      *
      *
      * @param array $columns -
      * @return \Providers\Core\QueryExtender -
      */

     public function grouping(array $columns){

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= (" GROUP BY " . $_columns);

        return $this;
     }

     /**
      * Executes an sql query
      *
      *
      *
      * @param integer $limit - value of LIMIT clause in SQL query
      * @param integer $offset - value of OFFSET clause in SQL query
      * @return mixed
      */

     public function exec($limit, $offset){

     	  $type = substr($this->queryString, 0, index_of($this->queryString, " ", 0));
     	  $result = NULL;
          $stoppers = array();

          if($offset > 0){
             $stoppers[] = " OFFSET {$offset}";
          }

          if($limit > 0){
             $stoppers[] = " LIMIT {$limit}";
          }

          switch (strtolower($type)){
          	case 'select':
                $this->queryString .= implode(',', $stoppers);
                $this->queryString = str_replace(' <;join>', '', $this->queryString);
                $this->queryString = str_replace(' <;distinct>', '', $this->queryString);
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

     /**
      * Wraps an SQL query attribute [column_name, table_name] in quotes
      *
      *
      *
      * @param string $attributeName -
      * @param string $char -
      * @return string
      * @api private
      */

     private function wrap($attributeName, $char = "`"){

          $db_driver = $GLOBALS['app']->getDBDriver();

          if(!isset($char) || empty($char)){

                $char = "`";
          }

          if($db_driver != "mysql"){

                $char = '"';
          }

          if(strlen($attributeName) === 0){
                
                return $attributeName;
          }

          return ($char.$attributeName.$char);
     }

     private function addTablePrefix($column){

          return $this->wrap($this->attribs['table']) . '.' . $this->wrap($column);
     }

     private function prepareInsertPlaceholder($props){

        $this->paramValues = $props;

        return array_fill(0, count($props), "? ");
     }

     private function prepareDeletePlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        if(is_array($this->paramValues)){
               $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
        }else{
               $this->paramValues = array_pluck($rawValues, 1);
        }

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);

        return array_mapper('update_placeholder', $sqlProps);
     }

     private function prepareSelectPlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        if(is_array($this->paramValues)){
               $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
        }else{
              $this->paramValues = array_pluck($rawValues, 1);
        }

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);

        return array_mapper('update_placeholder', $sqlProps);
     }

     private function prepareUpdatePlaceholder($props){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        if(is_array($this->paramValues)){
          
            $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
        }else{

            $this->paramValues = array_pluck($rawValues, 1);
        }

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);

        return array_mapper('update_placeholder', $sqlProps);
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