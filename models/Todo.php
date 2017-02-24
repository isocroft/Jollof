<?php

class Todo extends Model {

    protected $table = 'tbl_todos';

    protected $primaryKey = 'id';

    protected $relations = array(
 		
 		'TodoList' => '@list_id' // <OutSide Model> =====> hasMany ====> <@ThisModelForeginKey> , <OuttSideModel> ======> belongsTo =======> <#ThisModelPrimaryKey>
    );

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();
    }

}


?>