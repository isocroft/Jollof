<?php

class UserThrottle extends Model /* implements AuthService */ {

    protected $table = 'tbl_user_throttle';

    protected $primaryKey = 'throttle_id';

    protected $relations = array(
       'User' => 'user_id'
    );

    public function __construct(){

        parent::__construct();
    }
    

}


?>