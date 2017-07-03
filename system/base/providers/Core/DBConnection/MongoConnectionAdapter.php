<?php

namespace Providers\Core\DBConnection;

class MongoConnectionAdapter extends BaseConnectionAdapter{
	
	public function __construct($dbName = NULL, $driverClass = ''){

		parent::__construct($dbName, $driverClass);

	}

	protected function makeConnection(array $config){ 

		$dbname = $this->getDBName();

		$type = $this->getType();

		$dbOptions = array(
						'connectionTimeoutMS' => 30000/*, 
						'replicaSet' => '[replicaSetName]'*/
					);

		if(!isset($type) 
			|| !class_exists($type)){

			$type = '\\MongoClient';
			
			if(!class_exists($type)){
				;
			}
		}

		if(index_of($type, 'MongoClient') > 0){

				$connectionString = 'mongodb://' . $config['hostname'] . ':' . $config['port'] . '/' . $dbname;

				$dbOptions['username'] = $config['username'];
				$dbOptions['password'] = $config['password'];
		}else{
		

		 		$connectionString = 'mongodb://' . $config['username'] . ':' . $config['password'] . '@' . $config['hostname'] . ':' . $config['port'] . '/' . $dbname;
		}

		$dbo = new $type($connectionString, $dbOptions);

		if(method_exists($dbo, 'selectDB')){

			$dbo = $dbo->selectDB($dbname);

		}
		
		/*if(empty($dbo)){
			
			$dbo = $dbo->{$dbname};
		}*/

		return $dbo;
	}
}

?>

