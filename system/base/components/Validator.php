<?php

/*!
 * Jollof (c) Copyright 2016
 *
 *{Validator.php}
 *
 */

use \Providers\Tools\InputFilter as Filter;

class Validator{

    const FAILED = "failed!";

    protected static $instance = NULL;

    protected $filter;

    protected $allowed_errors;

    protected $errors;

    protected $fieldHasError;

    public $allowed;

    private function __construct(){

         $this->filter = new Filter();

         $this->fieldHasError = FALSE;

         $this->errors = array();

         $this->allowed = NULL;

         $this->allowed_errors = FALSE;

    }

    public static function createInstance(){

        if(static::$instance == NULL){
            static::$instance = new Validator();
            return static::$instance;
        }    
    }

    public static function getErrors(){
	      
        if(count(static::$instance->errors) == 0){
		          return NULL;
		    }

        return static::$instance->errors;
    }

    public function filterEmail($email){

        return $this->filter->sanitizeInput($email, 4);
    }

    public static function checkAndSanitize(array $data, array $fieldRules){ // $data should be the data from $_POST super global always...
         $valid = TRUE;
         $results = array();
         $callbacks = NULL;
         $fieldvalue = '';
         $pattern = '';
         
         // extract all callbacks

         foreach($fieldRules as $fieldname => $rule){

            static::$instance->allowed = NULL;
            static::$instance->fieldHasError = FALSE;

            $callbacks = NULL;
            $fieldvalue = '';
            $pattern = '';

            if(is_array($rule)){
               static::$instance->allowed = $rule['allowed'];
               $callbacks = explode("|", $rule['rule']);
			         $callbacks[] = 'useAllowed';
            }else if(is_string($rule)){
               $index = index_of($rule, '/');
               if($index > -1){
                  $pattern = substr($rule, $index);
               }
               $callbacks = explode("|", substr($rule, 0, $index-1));
            }else{
               
                throw new Exception("Validator: could not process ['". $rule['rule'] . "'] for $fieldname");
            }
  
            // setup callbacks to work on each field(s)
            foreach($callbacks as $callback){
               $fieldvalue = $data[$fieldname];
               $fieldvalue = trim(strip_tags(htmlspecialchars($fieldvalue, ENT_QUOTES, 'UTF_8')));
               $valid = $callback($fieldvalue, $fieldname, $pattern, static::$instance);
               if(!is_bool($valid)){
                    static::$instance->errors[] = $valid; // read out the error message in $valid first!!
				            $valid = TRUE; // then set it to the default boolean value
                    static::$instance->fieldHasError = $valid;
                    break;
               }
            }

            if(static::$instance->fieldHasError){
                continue;
            }

            $magic_quotes_active = get_magic_quotes_gpc();
             
            if($magic_quotes_active){

                  $fieldvalue = stripslashes($fieldvalue);
            }
             
            $results[$fieldname] = $fieldvalue;
            
         }

         return $results;   
    }
    

}

?>
