<?php

class Todo extends Model {

    protected $table = 'tbl_todos';

    protected $primaryKey = 'id';

    protected $relations = array(

    	'User' => '@assignee', // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey>
 		
 		'TodoList' => '@list_id' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey> 
    );
    
    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct(); 

        // Example usage (1): 

        // $resultset = User::fetchWithOrder(Todo::$class, array('id' => array('=', '1234566722334'));
        	// The above code will retrieve all [todos] assigned to a given user (email)

        // SELECT * FROM `tbl_users` LEFT JOIN `tbl_todos` ON `tbl_users`.`id` = `tbl_todos`.`assignee` WHERE `id` = '1234566722334'

        // Example usage (2):

        // $resultset = TodoList::fetchWithOrder(Todo::$class, array('user_id' => array('=', '1234566722334'), 'project_id' => array('=', '9999999')), array('created_at')); 	
        	// The above code will retrieve all [todos] created by a given user for a given project (logged user)

        // SELECT * FROM `tbl_todos_list` LEFT JOIN `tbl_todos` ON `tbl_todos_list`.`id` = `tbl_todos`.`list_id` WHERE `user_id` = '13420202303393' AND `project_id` = '9999999' ORDER BY `created_at` ASC

        // Example usage (3):

        // $project = Project::create(array('author' => '33637718923838', 'name' => '<DEFALT:personal>', 'type' => 'short-term', 'start_date' => '', 'end_date' => ''));
        // $list = TodoList::create(array('user_id' => '33637718923838', 'title' => '<DEFAULT:general>' 'project_id' => $project['pkey']));
        // Todo::create(array('list_id' => $list['pkey'], 'assignee' => $user['id']));
    }

}


?>