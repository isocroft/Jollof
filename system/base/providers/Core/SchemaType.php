<?php

namespace Providers\Tools;

class SchemaColumnType {

		protected $nulls = array(
			'true' => 'NULL',
			'false' => 'NOT NULL'
		);

		protected $defaults = array(
			'null' => '',
			'ctime' => 'CURRENT_TIMESTAMP'
		);
	
		public function __construct(){

		}

		public function text($default){

			return "TEXT '{$default}'";
		}

		public function timestamp($length, $isNull = false, $default = 'ctime'){

			$nkey = json_encode($isNull);

			return "TIMESTAMP " . $this->nulls[$nkey] . $this->getDefaults($default);
		}

		public function integer($length, $isNull = false, $default = 'null'){

			if(!is_integer($length)){

				throw new Exception("");
			}

			$nkey = json_encode($isNull);

			return "INT({$length}) " . $this->nulls[$nkey] . $this->getDefaults($default);
		}

		public function double($length, $isNull, $default){

			if(!is_float($length)){

				throw new Exception("");
			}

			return "DOUBLE($length) " . $this->nulls[$nkey] . $this->getDefault($default);
		}

		protected function getDefaults($default){
			if(!array_key_exists($default, $this->defaults)){
				if(is_integer($default) || is_string($default)){
					return strval($default);
				}
				throw new Exception("Schema Default Not Supported");
			}
			return $this->defaults[$default];
		}
}

?>