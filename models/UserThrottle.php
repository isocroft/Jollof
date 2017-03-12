<?php

class UserThrottle extends Model /* implements Verifiable */ {

    protected $table = 'tbl_user_throttles';

    protected $primaryKey = 'id';

    protected $relations = array(

    );

    protected $pivotTable = null;

    protected $autoPrimaryKey = false;

    public function __construct(){

        parent::__construct();
    }


}


?>