<?php

class User extends Model {

    protected $table = 'tbl_app_users';

    protected $primaryKey = 'id';

    protected $relations = array(
 
    );

    public function __construct(){
        
        parent::__construct();
    }    

}


?>
