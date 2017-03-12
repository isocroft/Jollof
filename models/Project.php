<?php

class Todo extends Model {

    protected $table = 'tbl_projects';

    protected $primaryKey = 'id';

    protected $relations = array(

    	'TodoList' => '#id', // <OutSideModel> =====> belongsTo ====> <@ThisModelPrimaryKey>
 		
 		'User' => '@author' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey> 
    );
    
    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct(); 

    }
}

?>