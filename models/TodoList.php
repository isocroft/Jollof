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

        // $resultset = User::fetchWith(TodoList::class, array('email' => 'user@example.com'));
        	// The above code fetches the list of all [todo-lists] for all projects created by a given user (logged user - email)

            // SELECT * FROM `tbl_users` LEFT JOIN `tbl_todos_list` ON `tbl_users`.`id` = `tbl_todos_list`.`user_id` WHERE `email` = 'user@example.com'


        // $wheres = array_pluck($resultset, 'project_id', 'id');

        // $resultset = Project::whereByOr(array_flatten($wheres), array('id', name'));
    }

}


?>