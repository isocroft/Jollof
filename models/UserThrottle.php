<?php

class UserThrottle extends Model /* implements AuthService */ {

    protected $table = 'tbl_user_throttles';

    protected $primaryKey = 'id';

    protected $relations = array(

    );

    public function __construct(){

        parent::__construct();
    }
    

}


?>