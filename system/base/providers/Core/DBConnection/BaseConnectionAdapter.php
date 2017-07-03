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
	

	  /**
	   * @var string
	   */
		protected $type;


	  /**
	   * @var string
	   */
		protected $dbname;

		/**
		 * Constructor
		 *
		 *
		 * @param string $db_name
		 * @param string $driver_class_name
		 * @throws InvalidArgumentException
		 *
		 */

		public function __construct($db_name, $driver_class_name){

			if(empty($db_name) || !isset($db_name) || !is_string($db_name)){

				throw new InvalidArgumentException("Expected [string] got [null]");
			}


			if(class_exists('\\' . $driver_class_name)){

				$this->type = '\\' . $driver_class_name;
				
			}else{
				if($driver_class_name == 'Mongo'){

					$this->type = '\\MongoClient'; 
				}
			}

			$this->dbname = $db_name;

		}

		public function getType(){

			return $this->type;
		}

		public function getDBName(){

			return $this->dbname;
		}

		/**
		 *
		 *
		 *
		 * @param $config
		 *
		 */

		public function connect(array $config){ 

			if(!array_key_exists('settings', $config)){

				$config['settings'] = array(

				);
			}

			return $this->makeConnection($config);

		}

		abstract protected function makeConnection(array $config);
}

?>