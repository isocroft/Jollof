<?php

class User extends Model {

    protected $table = 'tbl_users';

    protected $primaryKey = 'id';

    protected $relations = array(
 		'TodoList' => 'id'
    );

    public function __construct(){

        parent::__construct();
    }

}


?>
