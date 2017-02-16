<?php

namespace Providers\Core\DBConnection;

class PgSqlConnectionAdapter extends BaseConnectionAdapter{
	
	public function __construct($dbName = NULL, $unixSocket = NULL){

		parent::__construct();
	}
}

?>

