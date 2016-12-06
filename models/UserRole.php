<?php

class UserRole extends Model {

    protected $table = 'tbl_user_role';

    protected $primaryKey = 'role_id';

    protected $relations = array(
       'User' => 'user_id'
    );

    public function __construct(){

        parent::__construct();
    }
    

}


?>
