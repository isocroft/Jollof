<?php

namespace Providers\Core\DBConnection;

class PgSqlConnectionAdapter extends BaseConnectionAdapter{
	
	public function __construct($dbName = NULL, $driverClass = ''){

		parent::__construct($dbName, $driverClass);

	}

	protected function makeConnection(array $config){ 

		$dbname = $this->getDBName();

		$type = $this->getType();

		$connectionString = 'pgsql:host=' . $config['hostname'] . ';dbname=' . $dbname . ';charset=' . $config['charset'];

		if(isset($config['port'])){
			$connectionString .= ';port=' . $config['port'];
		}

		if(!isset($type)){

			return NULL;
		}

		$dbo = new $type($connectionString, $config['username'], $config['password'], $config['settings']);

		if(isset($config['schema'])){
			$dbo->prepare("SET search_path TO '" . $config['schema'] . "'")->execute();
		}

		return $dbo;
	}
}

?>

