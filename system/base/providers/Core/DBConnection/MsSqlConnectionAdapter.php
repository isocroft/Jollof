<?php

namespace Providers\Core\DBConnection;

class MsSqlConnectionAdapter extends BaseConnectionAdapter{
	
	public function __construct($dbName = NULL, $driverClass = ''){

		parent::__construct($dbName, $driverClass);

	}

	protected function makeConnection(array $config){ 

		$dbname = $this->getDBName();

		$type = $this->getType();

		if(get_os() === 'windows'){

			$connectionString = 'mssql:host=' . $config['hostname'] . ',' . $config['port'];

		}else{

			$connectionString = 'mssql:host=' . $config['hostname'] . ':' . $config['port'];

		}

		
		$connectionString .= ';dbname=' . $dbname;	

		if(!isset($type)){

			return NULL;
		}

		$dbo = new $type($connectionString, $config['username'], $config['password'], $config['settings']);

		return $dbo;
	}
}

?>

