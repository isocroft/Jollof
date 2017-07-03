<?php

class Todo extends Model {

    protected $table = 'tbl_todos';

    protected $primaryKey = 'id';

    protected $relations = array(

    	// 'User' => '@assignee', 
 		
 		'TodoList' => '@list_id' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey> 
    );
    
    protected $pivotTable = NULL;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct(); 

    }

}


?>