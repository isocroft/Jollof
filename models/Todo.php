<?php

class Todo extends Model {

    protected $table = 'tbl_todos';

    protected $primaryKey = 'id';

    protected $relations = array(
 		'TodoList' => 'list_id'
    );

    public function __construct(){
        
        parent::__construct();
    }    

}


?>