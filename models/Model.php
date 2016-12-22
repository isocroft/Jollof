<?php

/*!
 * Jollof (c) Copyright 2016
 *
 *
 * {Model.php}
 *
 */

use \Contracts\Policies\DBAccessInterface as DBInterface;
use \Providers\Core\QueryBuilder as Builder;


class Model implements DBInterface {

     protected $table = 'NULL';

     protected $primaryKey = 'NULL';

     protected $builder = NULL;

     protected $relations = array(

     );

     public function __construct(){

          $envfile = $env['app.path.base'] . '.env';

          if(file_exists($envfile)){

              $app->setDBConnection($envfile);

          }else{

               throw new \Exception("Cannot create Model Instance >> Database Settings Not Found");
               
          }    
     }

     protected function setBuilder(Builder $builder){
 
          $this->builder = $builder;
     }

     protected function rawGet(array $columns = array(), array $clauseProps = array(), $conjunction = 'and'){
                ;
     }

     protected function get(array $columns = array(), array $clauseProps = array(), $conjunction = 'and'){

         return $this->builder->select($columns, $clauseProps, $conjunction);
     }

     protected function set(array $values = array(), array $clauseProps = array()){
        
        return $this->builder->insert($values, $clauseProps);
     }

     protected function let(array $columnValues = array(), $clauseProps = array(), $conjunction = 'and'){

        return $this->builder->update($columnValues, $clauseProps, $conjunction);
     }

     protected function del(array $columns = array(), $clauseProps = array()){

        return $this->builder->delete($columns, $clauseProps);
     }

     public function getAttributes(){

         return array('table' => $this->table, 'key' => $this->primaryKey, 'relations' => $this->relations);
     }

}


?>
