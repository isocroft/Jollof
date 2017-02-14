<?php

/*!
 *
 * Jollof (c) Copyright 2016
 *
 * Based on 
 * https://github.com/usmanhalalit/pixie/blob/master/src/Pixie/ConnectionAdapters/BaseAdapter.php
 *
 * {BaseConnectionAdapter.php}
 */

namespace Providers\Core\DBConnection;

use \InvalidArgumentException;

abstract class BaseConnectionAdapter{
	
		protected $type;

		protected $dbname;

		protected $unixsocket;

		public function __construct($dbName, $unixSocket = NULL){

			if(empty($dbName) || !isset($dbName) || !is_string($dbName)){

				throw new InvalidArgumentException("Expected [string] got [null]");
			}

			$this->dbname = $dbName;

			$this->unixsocket = $unixSocket;
		}

		public function getType(){

			return $this->type;
		}

		public function getDBName(){

			return $this->dbname;
		}


		public function connect(array $config){ /* Will include only {hostname} {port} {username} {password} {charset} */

			if(!array_key_exists('settings', $config)){

				$config['settings'] = array(

				);
			}

			return $this->makeConnection($config);

		}

		abstract protected function makeConnection(array $config);
}

?>