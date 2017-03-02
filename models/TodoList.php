<?php

class TodoList extends Model {

    protected $table = 'tbl_todos_list';

    protected $primaryKey = 'id';

    protected $relations = array(

 		'User' => '@user_id' // <OutSide Model> =====> hasMany ====> <@ThisModelForeginKey>
    );

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();
    }

}


?>