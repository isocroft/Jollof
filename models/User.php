<?php

class User extends Model {

    protected $table = 'tbl_users';

    protected $primaryKey = 'id';

    protected $relations = array(

    	'Todo' => '#id', // <OuttSideModel> ======> belongsTo =======> <#ThisModelPrimaryKey>

 		'TodoList' => '#id', // <OuttSideModel> ======> belongsTo =======> <#ThisModelPrimaryKey>
 		'UserRole' => '#id' // <OuttSideModel> ======> belongsTo =======> <#ThisModelPrimaryKey>
    );

    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();
    }

}


?>
