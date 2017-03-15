<?php

class Todo extends Model {

    protected $table = 'tbl_todos';

    protected $primaryKey = 'id';

    protected $relations = array(

    	// 'User' => '@assignee', 
 		
 		'TodoList' => '@list_id' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey> 
    );
    
    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct(); 

        // Example usage (1): 

        // $resultset = Todo::whereBy(array('assignee' => array('=', 'user@example.com'));
        	// The above code will retrieve all [todos] assigned to a given user (logged user - email)

        // SELECT * FROM `tbl_todos` WHERE `assignee` = 'user@example.com'

        // Example usage (2):

        // $resultset = TodoList::fetchWithOrder(Todo::class, array('user_id' => array('=', '1e253fc4672bcdd61369aab8c1534bd1f08c'), 'project_id' => array('=', '45a2cd23f08bbd6477d2ff89715cba32de')), array('created_at')); 	
        	// The above code will retrieve all [todos] created by a given user (logged user) for a given project (logged user)

        // SELECT * FROM `tbl_todos_list` LEFT JOIN `tbl_todos` ON `tbl_todos_list`.`id` = `tbl_todos`.`list_id` WHERE `user_id` = '1e253fc4672bcdd61369aab8c1534bd1f08c' AND `project_id` = '45a2cd23f08bbd6477d2ff89715cba32de' ORDER BY `created_at` ASC

        // Example usage (3):

        // $user = Auth::user(); /* user from session */

        // $project = Project::create(array('name' => 'app testing', 'mode' => 'short-term'));
        // $list = TodoList::create(array('user_id' => '33637718923838', 'project_id' => $project['pkey']));
        // Todo::create(array('list_id' => $list['pkey'], 'assignee' => $user['email']));
    }

}


?>