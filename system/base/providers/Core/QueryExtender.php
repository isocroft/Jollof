<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryExtender.php}
 *
 */

namespace Providers\Core;

use \PDO;
use \UnexpectedValueException;
use \Model;

class QueryExtender {
     
     /**
      * @var string - the actual SQL query string being built out
      */

     protected $queryString;

     /**
      * @var array - attributes of the base model [primary_key, table_name, foreign_key]
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
      * @var array - all conjunctions which are allowed in a query
      */

     protected $allowedConjunctions = array(
        'AND',
        'OR',
        'NOT',
        'OR NOT'
     );
    
     /**
      * @var array - all operators which are allowed in a query
      */
    protected $allowedOperators = array(
        'LIKE',
        'BETWEEN',
        'IN',
        '>',
        '<>',
        '=',
        '<'
    );

    /**
     * Constructor
     *
     *
     * @param PDO $connection - PDO connection object
     * @param array $paramTypes - PDO parameter type constants
     * @api public
     */

     public function __construct(PDO $connection, array $paramTypes){

         $this->queryString = '';

         $this->paramValues = NULL;

         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

     }

     /**
      * Builds out a select query
      *
      *
      * @param array $columns - columns for SELECT query
      * @param array $clauseProps - column/value pairs for WHERE clause in SELECT query
      * @param string $conjunction - conjunction for WHERE clause
      * @return \Providers\Tools\QueryExtender -
      *
      * @throws UnexpectedValueException
      */
     
     public function get($columns, $clauseProps, $conjunction){ 
     	
        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " ". strtoupper($conjunction) ." ";

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        foreach ($clauseProps as $key => $value) {
            if(is_array($value)){
                if(count($value) > 0){
                    # check that WHERE clause operator supplied is allowed !!
                    if(!(in_array(strtoupper($value[0]), $this->allowedOperators))){
                        throw new UnexpectedValueException();
                    }
                }
            }
        }

        # start building query string -> SELECT
        $this->queryString .= "SELECT " . $_columns . " FROM " . $table . (count($clauseProps) > 0? " <;join> WHERE " . implode($conjunction, $this->prepareSelectPlaceholder($clauseProps)) : " <;join>");

        return $this;
     }

     /**
      * Builds out an insert query
      *
      *
      * @param array $columns - columns for INSERT query
      * @param array $values -  values for the columns for INSERT query
      * @param array $clauseProps - columns for update where duplicate key exists
      * @return \Providers\Tools\QueryExtender -
      */

     public function set($columns, $values, $clauseProps = array()){

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        # start building query string -> INSERT
        $this->queryString .= "INSERT INTO " . $table . "(" . $_columns . ") VALUES (" .  implode(', ', $this->prepareInsertPlaceholder($values)) . ")";

        if(count($clauseProps) > 0){
            $this->queryString .= " ON DUPLICATE KEY UPDATE ";
            $this->queryString .= implode(', ', $this->prepareUpdatePlaceholder($clauseProps);
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
      * @return \Providers\Tools\QueryExtender -
      *
      * @throws UnexpectedValueException
      */

     public function let($columnValues, $clauseProps, $conjunction){

        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " ".strtoupper($conjunction) ." ";

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
      * @param $clauseProps - column/value pairs for WHERE clause in UPDATE query
      * @return \Providers\Tools\QueryExtender - 
      */

     public function del($columns){

        # wrap the table name with quotes like so: `table`
        $table = $this->wrap($this->attribs['table']);

        # start building query string -> DELETE
     	  $this->queryString .= "DELETE " . implode(', ', $columns) . " FROM " . $table;

        return $this;
     }

     /**
      *
      * 
      *
      *
      * @param \Model $model -
      * @param string $joinType - 
      * @return \Providers\Tools\QueryExtender -
      */

     public function with(Model $model, $joinType = 'inner'){

         if(strlen($this->queryString) == 0){

             return $this;
         }

         $joinType = strtoupper($joinType);

         $__atrribs = $model->getAttributes();

         # wrap the table name with quotes like so: `table`
         $table = $this->wrap($this->attribs['table']);

         $joinTable = $this->wrap($__atrribs['table']);

         $parentReference = $this->wrap($this->attribs['key']);

         $childReference = $this->wrap($__attribs['relations'][get_class($model)]);

         # start building query string -> {INNER|LEFT|OUTER RIGHT|OUTER LEFT|CROSS} JOIN
     	   $joinExp = " {$joinType} JOIN `{$joinTable}` ON `{$table}`.`{$parentReference}` = `{$joinTable}`.`{$childReference}`";

         $this->queryString = str_replace(' <;join>', $joinExp, $this->queryString);

     	 return $this;
     }

     /**
      *
      * 
      *
      *
      * @param array $colums - ordering columns for SELECT query
      * @param bool $ascending - flag to specify sorting preference
      * @return \Providers\Tools\QueryExtender -
      */

     public function ordering(array $columns, $ascending = FALSE){

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

     /**
      * Builds out the HAVING clause to a SELECT query
      * 
      *
      *
      * @param array $clauseProps - 
      * @return \Providers\Tools\QueryExtender -
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
      * @return \Providers\Tools\QueryExtender -
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

     public function exec($limit = 0, $offset = 0){

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

          if(strlen($attributeName) === 0){
                return $attributeName;
          }

          return $char.$attributeName.$char;
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
         
        return array_map('update_placeholder', $sqlProps);
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
         
        return array_map('update_placeholder', $sqlProps);
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