<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryExtender.php}
 *
 */

namespace Providers\Core;

use \Model;
use \PDO;
use \UnexpectedValueException;

class QueryExtender {
     
     /**
      * @var string - the actual SQL query being built out
      */

     protected $queryString;

     /**
      * @var array - attributes of the base model [primary_key, table_name, foreign_key]
      */

     protected $attribs;

     /**
      * @var array
      */

     protected $paramValues;

     /**
      * @var array
      */

     protected $havingValues;

     /**
      * @var array - param types which are need by a PDO prepared statement [int,str]
      */

     protected $paramTypes;

     /**
      * @var array - for duplicate key cases for INSERT queries
      */

     protected $onDuplicateKeyValues;

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
     * @param PDO $connection
     * @param array $paramTypes
     * @api
     */

     public function __construct(PDO $connection, array $paramTypes){

         $this->queryString = '';

         $this->paramValues = NULL;

         $this->havingValues = NULL;

         $this->onDuplicateKeyValues = NULL;

         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

     }

     /**
      * Builds out a select query
      *
      *
      * @param array $columns - 
      * @param array $clauseProps - where clause column names and values
      * @param string $conjunction 
      * @return
      *
      * @throws UnexpectedValueException
      */
     
     public function get($columns, $clauseProps, $conjunction){ 
     	
        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " ". strtoupper($conjunction) ." ";

        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= "SELECT " . $_columns . " FROM " . $table . (count($clauseProps) > 0? " <;join> WHERE " . implode($conjunction, $this->prepareSelectPlaceholder($clauseProps)) : " <;join>");

        return $this;
     }

     /**
      * Builds out an insert query
      *
      *
      * @param array $columns - 
      * @param array $values -
      * @param array $clauseProps - 
      * @return
      */

     public function set($columns, $values, $clauseProps = array()){

        $table = $this->wrap($this->attribs['table']);

        $_columns = implode(', ', array_map(array(&$this, 'wrap'), $columns));

        $this->queryString .= "INSERT INTO " . $table . "(" . $_columns . ") VALUES (" .  implode(', ', $this->prepareInsertPlaceholder($values)) . ")";

        if(count($clauseProps) > 0){
            $this->queryString .= " ON DUPLICATE KEY UPDATE ";
            $this->queryString .= implode(', ', $this->prepareUpdatePlaceholder($clauseProps, TRUE));
        }

        return $this;
     }

     /**
      * Builds out an update query
      *
      *
      * @param array $columnValues - 
      * @param array $clauseProps - 
      * @param string $conjunction -
      * @return
      *
      * @throws UnexpectedvalueException
      */

     public function let($columnValues, $clauseProps, $conjunction){

        if(!in_array($conjunction, $this->allowedConjunctions)){
            throw new UnexpectedValueException();
        }

        $conjunction = " ".strtoupper($conjunction) ." ";

        $table = $this->wrap($this->attribs['table']);

        $this->queryString .= ("UPDATE " . $table . " SET " . (implode(', ', $this->prepareUpdateSetPlaceholder($columnValues))));

        if(count($clauseProps) > 0){
            $this->queryString .= " WHERE ". implode($conjunction, $this->prepareUpdatePlaceholder($clauseProps));
        }

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

     	 $joinExp = " {$joinType} JOIN `{$joinTable}` ON `{$table}`.`{$parentReference}` = `{$joinTable}`.`{$childReference}`";

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

     /**
      * Executes an sql query
      *
      *
      * @param integer $offset - value of OFFSET clause in SQL query
      * @param integer $limit - value of LIMIT clause in SQL query
      * @return mixed
      */

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
                 $result = db_put($this->connection, $this->paramTypes, $this->queryString, $this->parmeterizeValues($this->paramValues), $this->parameterizeValues($this->onDuplicateKeyValues));
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
       
        return array_fill(0, count($props), "?");
     }

     private function prepareUpdateSetPlaceholder($colVals){

        $rawValues = array_values($colVals);

        $rawKeys = array_keys($colVals);

        if(is_array($this->paramValues)){
               $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
        }else{
               $this->paramValues = array_pluck($rawValues, 1); 
        }

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);
         
        return array_map('update_placeholder', $sqlProps);
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

        if(is_array($this->paramValues)){
               $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
        }else{
            $this->paramValues = array_pluck($rawValues, 1); 
        } 

        $sqlProps = array_combine(array_map(array(&$this, 'wrap'), $rawKeys), $rawValues);
         
        return array_map('update_placeholder', $sqlProps);
     }

     private function prepareUpdatePlaceholder($props, $extra = FALSE){

        $rawValues = array_values($props);

        $rawKeys = array_keys($props);

        if($extra === FALSE){
            if(is_array($this->paramValues)){
               $this->paramValues = array_merge($this->paramValues, array_pluck($rawValues, 1));
            }else{
                $this->paramValues = array_pluck($rawValues, 1); 
            }
        }else{
            $this->onDuplicateKeyValues = array_pluck($rawValues, 1);
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