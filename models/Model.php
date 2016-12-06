<?php

use \Contracts\Policies\DBAccessInterface as DBInterface;
use \Providers\Core\QueryBuilder as Builder;


class Model implements DBInterface {

     protected $table = 'NULL';

     protected $primaryKey = 'NULL';

     protected $builder = NULL;

     protected $relations = array(

     );

     public function __construct(){

          if(file_exists($GLOBALS['env']['app.path.base'] . '.env')){

              $app->setDBConnection($GLOBALS['env']['app.path.base'] . '.env');

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

     protected function set(array $values = array()){
        
        return $this->builder->insert($values);
     }

     protected function let(array $columns = array(), $conjunction = 'and'){

        return $this->builder->update($columns, $conjunction);
     }

     protected function del(array $columns = array()){

        return $this->builder->delete($columns);
     }

     public function getAttributes(){

         return array('table' => $this->table, 'key' => $this->primaryKey, 'relations' => $this->relations);
     }

}


?>
