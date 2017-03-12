<?php

class UserRole extends Model {

    protected $table = 'tbl_user_roles';

    protected $primaryKey = 'id';

    protected $relations = array(

       'User' => '@user_id' // <OutSideModel> =====> hasMany ====> <@ThisModelForeignKey>
    );

    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();
    }


}


?>
