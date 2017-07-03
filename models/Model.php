<?php

/*!
 * Jollof (c) Copyright 2016
 *
 *
 * {Model.php}
 *
 */

use \Contracts\Policies\DBAccessInterface as DBInterface;
use \Providers\Tools\SchemaObject as SchemaObject;

class Model implements DBInterface {

     public static $class = NULL;

     protected static $instance = NULL;

     protected $table = '';

     protected $primaryKey = '_id';

     protected $builder = NULL;

     protected $relations = array(

     );

     protected $pivotTable = null;

     protected $autoPrimaryKey = false;

     protected $schema = array(

     );

     public function __construct(SchemaObject $schemaObject = NULL){

               $schema = array();

               if(!is_null($schemaObject)){

                    $schema = $schemaObject->toArray();
               } 
      
              static::setInstance($this, $schema);
    
     }

     public function __destruct(){

              static::unsetInstance();
     }

     protected static function unsetInstance(){

              if(static::$instance){

                  static::$instance = NULL;
              }
     }

     protected static function setInstance(Model $m, array $scma){
    
            /* 
             *  A trick to instantiate an class without calling the constructor 
             *  >   credits: PHPUnit Framework Project - GitHub
             *
             * -- This trick has been modified for use in Jollof
             */

            global $app;

            if(!isset(static::$instance)){

                  $name = get_class($m);  

                  $m->schema = $scma;
              
                  $m->builder = $app->getBuilder($m->getAttributes(), $name);

                  static::$instance = unserialize(
                    sprintf('O:%d:"%s":0:{}', strlen(get_called_class()), $name)
                  );

                  static::$class = get_class(static::$instance);

                  static::$instance->builder = $m->builder;

            }else{

                  $m->builder = static::$instance->builder;

            } 

      }


    /**
     *
     *
     *
     *
     * @param array $columns
     * @param array $clauseProps
     * @param string $conjunction
     * @return Contracts\Policies\QueryProvider 
     * @throws RuntimeException
     */

     protected function get(array $columns = array(), array $clauseProps = array(), $conjunction = 'and'){

         if(is_null($this->builder)){
                throw new \RuntimeException("No Database Connection Found, Jollof [.env] File Probably Missing");
         }   

         return $this->builder->select($columns, $clauseProps, $conjunction);
     }


    /**
     *
     *
     *
     *
     * @param array $values
     * @param array $clauseProps
     * @return Contracts\Policies\QueryProvider 
     * @throws RuntimeException 
     */

     protected function set(array $values = array(), array $clauseProps = array()){

        if(is_null($this->builder)){
                throw new \RuntimeException("No Database Connection Found, Jollof [.env] File Probably Missing");
        }

        return $this->builder->insert($values, $clauseProps);
     }


    /**
     *
     *
     *
     *
     * @param array $columnValues
     * @param array $clauseProps
     * @param string $conjunction
     * @return Contracts\Policies\QueryProvider 
     * @throws RuntimeException
     */

     protected function let(array $columnValues = array(), $clauseProps = array(), $conjunction = 'and'){

            if(is_null($this->builder)){
                throw new \RuntimeException("No Database Connection Found, Jollof [.env] File Probably Missing");
            }

        return $this->builder->update($columnValues, $clauseProps, $conjunction);
     }


    /**
     *
     *
     *
     *
     * @param array $columns
     * @param array $clauseProps
     * @return \Contracts\Policies\QueryProvider
     * @throws RuntimeException
     */

     protected function del(array $columns = array(), $clauseProps = array()){

        if(is_null($this->builder)){
            throw new \RuntimeException("No Database Connection Found, Jollof [.env] File Probably Missing");
        }

        return $this->builder->delete($columns, $clauseProps);
     }


    /**
     * Retrieves all attributes of the model
     *
     *
     *
     * @param void
     * @return array 
     */

     public function getAttributes(){

         return array('table' => $this->table, 'key' => $this->primaryKey, 'relations' => $this->relations, 'autoKey' => $this->autoPrimaryKey);
     }

    /**
     *
     *
     *
     *
     * @param void
     * @return array
     * @throws RuntimeException 
     */

     public function installSchema(){

          if(is_null($this->builder)){
                throw new \RuntimeException("No Database Connection Found, [.env] File Probably Missing");
         }

          return $this->builder->createEntity($this->schema);
     }


    /**
     *
     *
     *
     *
     * @param void
     * @return bool 
     */

     public function bindSchema(){

          return true;
     }

    /**
     * Retrieves one or more tuples/rows from a Model table/entity/collection 
     * based on conditions and operator (AND).
     *
     *
     * @param array $clause -
     * @param array $cols -
     * @return array [resultset] 
     * @api
     */

     public static function whereBy(array $clause = array(), array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          $pkey = $attrs['autoKey']? intval($attrs['key']) :  $attrs['key'];

          return static::$instance->get($cols, $clause)->exec();
     }

     /**
     * Retrieves one or more tuples/rows from a Model table/entity/collection 
     * based on conditions and operator (OR).
     *
     *
     * @param array $clause -
     * @param array $cols -
     * @return array [resultset] 
     * @api
     */

     public static function whereByOr(array $clause = array(), array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          $pkey = $attrs['autoKey']? intval($attrs['key']) :  $attrs['key'];

          return static::$instance->get($cols, $clause, 'or')->exec();
     }


    /**
     * Retrieves the very first tuple/row from a Model table/entity/collection
     * based on conditions and operator (AND).
     *
     *
     * @param $clause - 
     * @param $cols - 
     * @return array [resultset]
     * @api
     */

     public static function first(array $clause = array(), array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          return static::$instance->get($cols, $clause)->exec(1);
     }

     /**
     * Retrieves the very first tuple/row from a Model table/entity/collection
     * based on conditions and operator (OR).
     *
     *
     * @param $clause - 
     * @param $cols - 
     * @return array [resultset]
     * @api
     */

     public static function firstOr(array $clause = array(), array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          return static::$instance->get($cols, $clause, 'or')->exec(1);
     }

    /**
     * Retrieves distinct columns from a Model table/entity/collection
     *
     *
     *
     * @param array $cols
     * @return array [resultset] 
     * @api
     */

     public static function fetchDistinct(array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          return static::$instance->get($cols, array())
                                        ->distinct()
                                            ->exec(0, 0);
     }

    /**
     * Retrieves all tuples/rows from a Model table/entity/collection using a join
     *
     *
     *
     * @param string $modelName - class name of join Model
     * @param array $clause -
     * @return array [resultset]
     */

     public static function fetchWith($modelName, array $clause = array(), $limit = 0){  

          return static::$instance->get(array('*'), $clause)
                        ->with($modelName)
                            ->exec($limit, 0);
     }

    /**
     * Retrieves all tuples/rows in an ordered manner 
     * from a Model collection/entity using a join.
     *
     *
     * @param string $modelName - class name of join Model
     * @param array $clause - column value(s) for where clause
     * @param array $orderCols - column name(s) for orderby clause
     * @param integer $limit - limit for number of rows retrieved
     * @return array [resultset]
     * @api
     */

     public static function fetchWithOrder($modelName, array $clause = array(), array $orderCols = array(), $limit = 0){

          return static::$instance->get(array('*'), $clause)
                            ->with($modelName)
                                ->ordering($orderCols, true)
                                    ->exec($limit, 0);
     } 

    /**
     * Updates a tuple/row from the Model table using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id
     * @return array [resultset]
     * @api 
     */

     public static function updateById($id = ''){

          $attrs = static::$instance->getAttributes();
          
          $clause = array();
          $pkey = $attrs['autoKey']? intval($attrs['key']) :  $attrs['key'];
          $clause[$pkey] = array('=' => $id);

          return static::$instance->let(array('*'), $clause)
                                        ->exec(0, 0);
     } 

    /**
     * Deletes a tuple/row from the Model table using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id - value for primary key for Model table
     * @return array [resultset]
     * @api 
     */

     public static function removeById($id = ''){

          $attrs = static::$instance->getAttributes();
          
          $pkey = $attrs['autoKey']? intval($attrs['key']) :  $attrs['key'];
          $clause = array();
          $clause[$pkey] = array('=' => $id);

          return static::$instance->del(array('*'), $clause)->exec(0, 0);
     }

    /**
     * Retrieves a tuple/row from the Model table/entity/collection using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id - value for primary key for Model table
     * @return array [resultset]
     * @api 
     */

     public static function findById($id = ''){

          $attrs = static::$instance->getAttributes();
          
          $pkey = $attrs['autoKey']? intval($attrs['key']) :  $attrs['key'];
          $clause = array();
          $clause[$pkey] = array('=' => $id);

          return static::$instance->get(array('*'), $clause)->exec(0, 0);
     }

    /**
     * Retrieves all tuples/rows from the Model collection/entity
     *
     *
     *
     * @param array $clause - column values for where clause
     * @param integer $limit - limit for number of rows retrieved
     * @param integer $offset - offset for number of rows retrieved 
     * @return array [resultset]
     * @api
     */

     public static function fetchAll(array $clause = array(), $limit = 0, $offset = 0){

          return static::$instance->get(array('*'), $clause)
                                        ->exec($limit, $offset);

     }

    /**
     * Retrieves all tuples/rows in an ordered manner 
     * from the Model table/entity/collection.
     *
     *
     * @param array $clause - column value(s) for where clause
     * @param array $orderCols - column name(s) for orderby clause
     * @param integer $limit - limit for number of rows retrieved
     * @param integer $offset - offset for number of rows retrieved
     * @return array 
     * @api
     */

     public static function fetchAllOrdered(array $clause = array(), array $orderCols = array(), $limit = 0, $offset = 0){

          return static::$instance->get(array('*'), $clause)
                                        ->ordering($orderCols, true)
                                            ->exec($limit, $offset);
     }

    /**
     * Updates a tuple/row in the Model table/entity/collection
     *
     *
     *
     * @param array $clause - coulmn(s) values for where clause
     * @param array $cols - column(s) to update
     * @return array 
     */

     public static function update(array $clause = array(), array $cols = array()){

          return static::$instance->let($cols, $clause)
                                            ->exec(0, 0);
     }

    /**
     * Inserts OR Updates a tuple/row in the Model table/entity/collection
     *
     *
     *
     * @param array $tuple - 
     * @param array $clause -
     * @return array [result]
     * @api
     */

     public static function upsert(array $tuple = array(), array $clause = array()){

          if(count($clause) == 0){
                $clause = NULL;
          }

          return static::create($tuple, $clause);
     }

    /**
     * Inserts a tuple/row into the Model table/entity/collection
     *
     *
     *
     * @param array $tuple -
     * @param array $updateCols -
     * @return array [result]
     * @api
     */

     public static function create(array $tuple = array(), array $updateCols = array()){

          $attrs = static::$instance->getAttributes();

          /* 
            check if the primary key field is included in
            the tuple/row info passed in as argument.
          */ 

          if(!$attrs['autoKey']){  

              if(!array_key_exists($attrs['key'], $tuple)){

                    $tuple[$attr['key']] = Helpers::randomCode(); 
              }

          }


          $rowId;

          if($updateCols === NULL){

                $rowId = static::$instance->set($tuple, array(), array())->exec(0, 0);

          }else{

               $rowId = static::$instance->set($tuple, $updateCols, array())->exec(0, 0);
          }     

          if(is_integer($rowId) && $rowId === 0){

                return array('pkey' => $tuple[$attr['key']]);
          }

          return array('pkey' => strval($rowId)); // If it is NULL, the atomic operation wasn't completed (rolled-back)
     }

}


?>
