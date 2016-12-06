<?php

class User extends Model /* implements AuthService */ {

    protected $table = 'tbl_app_users';

    protected $primaryKey = 'user_id';

    protected $relations = array(
       'Persona' => 'persona_id',
       'Partner' => 'partner_id'
    );

    public function __construct(){

        parent::__construct();
    }    

}


?>