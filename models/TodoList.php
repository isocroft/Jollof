<?php

class TodoList extends Model {

    protected $table = 'tbl_todos_list';

    protected $primaryKey = 'id';

    protected $relations = array(

    	'Project' => '@project_id', // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey>

    	'Todo' => '#id', // <OutSideModel> ======> belongsTo ====> <#ThisModelPromaryKey>

 		'User' => '@user_id' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey>
    );

    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();

        // Example usage (1):

        // $resultset = Project::fetchWithOrder(TodoList::$class, array('author' => '13420202303393'), array('created_at'));
        	// The above code fetches the list of all [todo-lists] for all projects created by a given user (logged user)
    }

}


?>