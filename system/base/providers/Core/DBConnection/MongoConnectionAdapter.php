<?php

namespace Providers\Core\DBConnection;

class MongoConnectionAdapter extends BaseConnectionAdapter{
	
	public function __construct($dbName = NULL, $driverClass = ''){

		parent::__construct($dbName, $driverClass);

	}

	protected function makeConnection(array $config){ 

		$dbname = $this->getDBName();

		$type = $this->getType();

		$connectionString = 'mongodb://' . $config['hostname'] . ':' . $config['port'] . '/' . $dbname;

		if(!isset($type)){

			return NULL;
		}

		$dbo = new $type($connectionString, array('username'=>$config['username'], 'password'=>$config['password']));

		if(method_exists($dbo, 'selectDB')){

			$dbo = $dbo->selectDB($dbname);
		}

		return $dbo;
	}
}

?>

