<?php

class Project extends Model {

    protected $table = 'tbl_projects';

    protected $primaryKey = 'id';

    protected $relations = array(

    	'TodoList' => '#id' // <OutSideModel> =====> belongsTo ====> <@ThisModelPrimaryKey>
 		
    );
    
    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct(); 

    }
}

?>