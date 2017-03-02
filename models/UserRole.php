<?php

class UserRole extends Model {

    protected $table = 'tbl_user_roles';

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
