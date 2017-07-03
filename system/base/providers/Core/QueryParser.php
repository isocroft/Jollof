<?php

/**
 * Jollof Framework (c) 2016
 *
 *
 * {QueryParser.php}
 *
 */

namespace Providers\Core;

use \Contracts\Policies\QueryProvider as QueryProvider;
// use \Mongo;
// use \MongoClient;
use \UnexpectedValueException;
use \Exception;
use \ReflectionClass;

/* This query provider is for NoSQL databases */

class QueryParser implements QueryProvider {

  	 /**
  	  * @var string - 
  	  */

  	 protected $queryType;

     /**
      * @var array - 
      */

     protected $fields;

  	 /**
      * @var array - attributes of the base model [primary_key, table_name, relations]
      */

     protected $attribs;

     /**
      * @var array - param types which are need by a Mongo query [int,str,bol,obj,arr]
      */

     protected $paramTypes;

     /**
      * @var array - parameter for a Mongo query
      */

     protected $params;

     /**
      * @var \Mongo - database connection object
      */

     protected $connection;

     /**
      * @var string - Model calss with relation
      */

     protected $ownerModel;

     /**
      * @var array - 
      */

     protected $sortColumns;

     /**
      * @var array - 
      */

     protected $queryOptions;

     /**
      * @var array - all conjunctions which are allowed in a query
      */

     protected $allowedConjunctions = array(
        '$and' => 'AND',
        '$or' => 'OR',
        '$_' => 'NOT',
        '$|' => 'OR NOT'
     );

     /**
      * @var array - all operators which are allowed in the {WHERE} clause of a query
      */

    protected $allowedOperators = array(
        '$lt' => '<',
        '$_' => '=',
        '$gte' => '>=',
        '$lte' => '<=',
        '$lt' => '>',
        '$ne' => '<>',
        '$gte|$lt' => 'BETWEEN',
        '$regex' => 'LIKE',
        '$$' => 'NOT LIKE',
        '$in' => 'IN'
    );

    /**
     * @var ReflectionClass - for obtaining the object of a class with just the class name
     */ 

     protected $reflClass;

	/**
     * Constructor
     *
     *
     * @param Mongo $connection - Mongo connection object
     * @param array $paramTypes - Mongo parameter type constants
     * @api public
     */

     public function __construct($connection, array $paramTypes, $modelName = NULL){

         $this->params =  array();

         $this->sortColumns = array();

         $this->queryOptions = array();

         $this->fields = NULL;

         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

         $this->reflClass = NULL;

         $this->ownerModel = $modelName;

     }

     public function setAttributes(array $schemaAttribs){

     	    $this->attribs = $schemaAttribs;

          // selects the collection if it exists
          $this->connection = $this->connection->selectCollection(
                                            $this->atrribs['table']
                                        );
          // or creates a new one if it doesn't
          if (empty($this->connection)){
	                  $this->connection =  $this->connection->createCollection(
                                      $this->attribs['table'], 
                                      array(
                                          'capped' => TRUE,
                                          'size' => 100*1024*1024, // 100MB
                                          'max' => 100
                                      )
                                );
          }

          /* $this->connection->ensureIndex('password', array('unique' => 1)); */

          /*
            $collections = array_map(array(&$this, ''), $this->connection->listCollections());

            if(fast_in_array($this->atrribs['table'], $collections)){
                  ;
            }
          */
     }

     protected function transformClause($value, $key, $args){

            $k = $args['keys'];
            
            $v = $args['values'];

            $op = $args['opr'];

            $vx = $key == '_id' && class_exists("\\MongoId") ? new \MongoId($value[1]) : $value[1];

            $ar = NULL;

            $ar = fast_in_array($value[0], $k)
                      ? array(
                          str_replace($k, $v, $value[0]),
                          $vx)
                      : $vx;
          

            if(!is_null($op)){
                $this->params[$op][$key] = $ar;
            }else{
                $this->params[$key] = $ar;
            }

     }

        
     /**
      * Builds out a select/find query
      *
      *
      * @param array $columns - columns for SELECT/FIND query
      * @param array $clauseProps - column/value pairs for WHERE clause in SELECT/FIND query
      * @param string $conjunction - conjunction for WHERE/CONDITION clause
      * @return \Providers\Core\QueryParser -
      *
      * @throws UnexpectedValueException
      */

      public function get(array $columns, array $clauseProps, $conjunction){

          $this->queryType = 'select';

	      	if(count($columns) == 1){ 
	      	 	 
              if($columns[0] == '*'){

                    $this->fields = array();
              }
              
          }   
          
          $ops = array_flip($this->allowedOperators);
	      	
          if(count($clauseProps) > 0){

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

                $conj = NULL;
	      	      
                if(count($clauseProps) > 0){
                    $conj = '$' . strtolower($conjunction);
                    $this->params[$conj] = array();
                }
                  
                if(array_walk(
                                $clauseProps, 
                                array(&$this, 'transformClause'), 
                                array(
                                      'keys' => array_keys($ops), 
                                      'values' => array_values($ops),
                                      'opr' => $conj
                                ))){
            
                      $this->fields = array_fill_keys($columns, TRUE);
                };

          }

          return $this;
      }

     /**
      * Builds out an insert query
      *
      *
      * @param array $columns - columns for INSERT query
      * @param array $values -  values for the columns for INSERT query
      * @param array $clauseProps - columns for update where duplicate key exists
      * @return \Providers\Core\QueryParser -
      */

      public function set(array $columns, array $values, $clauseProps = array()){

      		$this->params = array_combine($columns, $values);

      		$this->queryType = 'insert';

          return $this;
      }

     /**
      * Builds out an update query
      *
      *
      * @param array $columnValues - column/value pairs for SET clause in UPDATE query
      * @param array $clauseProps - column/value pairs for WHERE criteria in UPDATE query
      * @param string $conjunction - conjunction for WHERE clause
      * @return \Providers\Core\QueryParser -
      *
      * @throws UnexpectedValueException
      */

     public function let(array $columnValues, array $clauseProps, $conjunction){
     		
         $conj = NULL;

         $this->queryType = 'update';

         $ops = array_flip($this->allowedOperators);
          
          if(count($clauseProps) > 0){

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
                
                if(count($clauseProps) > 0){
                    $conj = '$' . strtolower($conjunction);
                    $this->params[$conj] = array();
                }
                  
                if(array_walk(
                                $clauseProps, 
                                array(&$this, 'transformClause'), 
                                array(
                                      'keys' => array_keys($ops), 
                                      'values' => array_values($ops),
                                      'opr' => $conj
                                ))){
            
                      $this->fields = array('$set' => $clauseValues); 
                };

          }      

     		  return $this;
     }

     /**
      * Builds out an delete query
      *
      *
      *
      * @param $columns - columns
      * @param $clauseProps - column/value pairs for WHERE clause in REMOVE query
      * @return \Providers\Core\QueryParser -
      */

     public function del(array $columns, array $clauseProps){

          $this->queryType = 'remove';
     	
          return $this;
     }

     /**
      *
      *
      *
      * 
      * 
      * @param string $modelName -
      * @param string $joinType -
      * @return \Providers\Core\QueryParser -
      * @throws Exception, UnxepectedValueException
      */

     public function with($modelName, $joinType = 'within'){
        
          return $this;
     }

     /**
      * Filters out distinct values in a FIND query
      *
      *
      * 
      * 
      * @param 
      * @return \Providers\Core\QueryParser -
      */

     public function distinct(){

          return $this;
     }

     /**
      * Builds out a create table query
      *
      *
      * 
      * 
      * @param ----
      * @return \Providers\Core\QueryParser -
      */

     public function table(){

          return $this;
     }

     /**
      *
      *
      *
      *
      * @param array $columns - ordering columns for FIND query
      * @param bool $ascending - flag to specify sorting preference
      * @return \Providers\Core\QueryExtender -
      */

     public function ordering(array $columns, $ascending = FALSE){

        $direction = ($ascending)? 1 : -1;
        $this->sortColumns = array();

        foreach ($columns as $index => $column) {
            $this->sortColumns[$column] = $direction;  
        }

        return $this;
     }

     /**
      * Builds out the HAVING clause to a FIND query
      *
      *
      *
      * @param array $clauseProps -
      * @return \Providers\Core\QueryExtender -
      * @throws \UnexpectedvalueException -
      */

     public function having(array $clauseProps){

        return $this;
     }
     

     /**
      * Executes an db query
      *
      *
      *
      * @param integer $limit - value of LIMIT
      * @param integer $offset - value of OFFSET
      * @return mixed
      */

     public function exec($limit, $offset){

     		$result = NULL;
        $cursor = NULL;

			switch($this->queryType){
				case "select":

     				$cursor = NULL;

            if(count($this->params) > 0

                && count($this->fields) > 0){
             
                $cursor = $this->connection->find($this->params, $this->fields);

            }else if(count($this->params) > 0){

                $cursor = $this->connection->find($this->params);

            }else{

                $cursor = $this->connection->find();
            }

            if(count($this->sortColumns) > 0){

                $cursor->sort($this->sortColumns);
            }

            if($limit > 0){

               if(!method_exists($cursor, 'limit')){

                    $cursor = $this->connection->findOne($this->params);
               
               }else{
               
                    $cursor = $cursor->limit($limit);
               } 
            }

            if($offset > 0){
                
                $cursor = $cursor->skip($offset);
            }

            $result = NULL;

            if(function_exists('iterator_to_array')){
              
                $result = iterator_to_array($cursor);

            }else{

                $result = array();

                foreach ($cursor as $doc) {
                    $result[] = $doc;
                }
            }    
				break;
				case "insert":

					  $result = $this->connection->insert($this->params, array());
				break;
        case "remove":

            $result = $this->connection->remove($this->params, false);
        break;
        case "update":

            $result = $this->connection->update($this->params, $this->fields, array()); 
        break;
			}  

      $this->params = array();
      $this->sortColumns = array();
      $this->queryOptions = array();
      $this->fields = NULL;

			return $result;   		
     }

}

?>