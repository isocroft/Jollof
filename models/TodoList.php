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

    }

}


?>