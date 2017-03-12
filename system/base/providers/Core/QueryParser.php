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


         $this->paramTypes = $paramTypes;

         $this->connection = $connection;

         $this->reflClass = NULL;

         $this->ownerModel = $modelName;

     }

     public function setAttributes(array $schemaAttribs){

     	$this->attribs = $schemaAttribs;

     	$this->connection = $this->connection->selectCollection($this->attribs['table']);
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

	      	if(count($clauseProps) == 0){
	      	 	
	      	 	if(count($columns == 1 
	      	 		&& $columns[0] == '*')){

	      	 		$this->queryType = 'select all';

	      	 		return $this;
	      	 	}
	      	}

	      	$this->params = $clauseProps;
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
      }

     /**
      * Builds out an update query
      *
      *
      * @param array $columnValues - column/value pairs for SET clause in UPDATE query
      * @param array $clauseProps - column/value pairs for WHERE clause in UPDATE query
      * @param string $conjunction - conjunction for WHERE clause
      * @return \Providers\Core\QueryParser -
      *
      * @throws UnexpectedValueException
      */

     public function let(array $columnValues, array $clauseProps, $conjunction){
     		
     		;
     }

     /**
      * Builds out an delete query
      *
      *
      *
      * @param $columns - columns
      * @param $clauseProps - column/value pairs for WHERE clause in DELETE query
      * @return \Providers\Core\QueryParser -
      */

     public function del(array $columns, array $clauseProps){
     	;
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

			switch($this->queryType){
				case "select all":

     				$result = $this->connection->find();
				break;
				case "insert":

					$result = $this->connection->insert($this->params);
				break;
			}  

			return $result;   		
     }


}

?>