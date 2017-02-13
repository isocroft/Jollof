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

     protected $table = 'NULL';

     protected $primaryKey = 'NULL';

     protected $builder = NULL;

     protected $relations = array(

     );

     protected $autoPrimaryKey = true;

     protected $schema = array(

     );

     public function __construct(SchemaObject $schemaObject){
      
              static::setInstance($this, $schemaObject);
    
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

            if(!isset(static::$instance)){

                  $m->$schema = $scma;
              
                  $m->builder = $app->getBuilder($m->getAttributes());

                  static::$instance = unserialize(
                    sprintf('O:%d:"%s":0:{}', strlen(get_called_class()), get_class($m))
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
     * @param 
     * @return  
     */

     protected function rawGet(array $columns = array(), array $clauseProps = array(), $conjunction = 'and'){
                ;
     }


    /**
     *
     *
     *
     *
     * @param array $columns
     * @param array $clauseProps
     * @param string $conjunction
     * @return Providers\Core\QueryExtender 
     */

     protected function get(array $columns = array(), array $clauseProps = array(), $conjunction = 'and'){

         return $this->builder->select($columns, $clauseProps, $conjunction);
     }


    /**
     *
     *
     *
     *
     * @param array $values
     * @param array $clauseProps
     * @return Providers\Core\QueryExtender 
     */

     protected function set(array $values = array(), array $clauseProps = array()){

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
     * @return Providers\Core\QueryExtender 
     */

     protected function let(array $columnValues = array(), $clauseProps = array(), $conjunction = 'and'){

        return $this->builder->update($columnValues, $clauseProps, $conjunction);
     }


    /**
     *
     *
     *
     *
     * @param array $columns
     * @param array $clauseProps
     * @return \Providers\Core\QueryExtender
     */

     protected function del(array $columns = array(), $clauseProps = array()){

        return $this->builder->delete($columns, $clauseProps);
     }


    /**
     *
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
     * @return void 
     */

     public function installSchema($mode = 'nosql'){

          $this->builder->setMode($mode);

          return $this->builder->tableCreate($this->schema);
     }


    /**
     *
     *
     *
     *
     * @param void
     * @return void 
     */

     public function bindSchema(){

          ;
     }

    /**
     * Retrieves one or more tuples/rows from a Model table 
     * based on conditions.
     *
     *
     * @param void
     * @return void 
     */

     public static function whereBy(array $clause, array $cols = array('*')){

          $attrs = static::$instance->getAttributes();

          $pkey = $attrs['key'];

          return static::$instance->get($cols, $clause)->exec();
     }

    /**
     *
     *
     *
     *
     * @param
     * @return
     */

     public static function fetchAllWith($modelName, array $clause){

          return static::$instance->get(array('*'), $clause)->with($modelName)->exec();
     }

    /**
     * Updates a tuple/row from the Model table using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id
     * @return array
     * @api 
     */

     public static function updateById($id = ''){

          $attr = static::$instance->getAttributes();
          $clause = array();
          $clause[$attr['key']] = array('=' =>$id);

          return static::$instance->let(array('*'), $clause)->exec(0);
     } 

    /**
     * Deletes a tuple/row from the Model table using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id
     * @return array
     * @api 
     */

     public static function removeById($id = ''){

          $attr = static::$instance->getAttributes();
          $clause = array();
          $clause[$attr['key']] = array('=' => $id);

          return static::$instance->del(array('*'), $clause)->exec(0);
     }

    /**
     * Retrieves a tuple/row from the Model table using the
     * value of the tables' primary key {$id}
     *
     *
     * @param string $id
     * @return array 
     * @api 
     */

     public static function findById($id = ''){

          $attr = static::$instance->getAttributes();
          $clause = array();
          $clause[$attr['key']] = array('=' => $id);

          return static::$instance->get(array('*'), $clause)->exec();
     }

    /**
     * Retrieves all tuples/rows from the Model table
     *
     *
     *
     * @param integer $limit
     * @param integer $offset
     * @return array 
     * @api
     */

     public static function fetchAll($limit = -1, $offset = -1){

          return static::$instance->get(array('*'))->exec($limit, $offset);
     }

    /**
     * Updates a tuple/row in the Model table
     *
     *
     *
     * @param array $tuple
     * @return array 
     */

     public static function update(array $clause, array $cols){

          return static::$instance->let($cols, $clause)->exec(0);
     }

    /**
     * Inserts OR Updates a tuple/row in the Model table
     *
     *
     *
     * @param array $tuple
     * @return array 
     */

     public static function createOrUpdate(array $tuple, array $clause){

          if(count($clause) == 0){
                $clause = NULL;
          }

          return static::create($tuple, $clause);
     }

    /**
     * Inserts a tuple/row into the Model table
     *
     *
     *
     * @param array $tuple
     * @return array 
     */

     public static function create(array $tuple, array $updateCols = NULL){

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

                $rowId = static::$instance->set($tuple)->exec(0);
          }else{

               $rowId = static::$instance->set($tuple, $updateCols)->exec(0);
          }     

          if(is_integer($rowId) && $rowId === 0){

                return array('pkey' => $tuple[$attr['key']]);
          }

          return array('pkey' => strval($rowId)); // If it is NULL, the atomic operation wasn't completed (rolled-back)
     }

}


?>
