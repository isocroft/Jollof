<?php

/*!
 * Jollof (c) Copyright 2016
 *
 *{Validator.php}
 *
 */

use \Providers\Tools\InputFilter as Filter;

final class Validator {

    /**
     * @var string | FAILED -
     */

    const FAILED = "failed!";

    /**
     * @var Validator -
     */

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

         $this->bounds = new \stdClass();

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

        return static::$instance->errors;
    }

    public function filterEmail($email){

        return $this->filter->sanitizeInput($email, 4);
    }

    public static function hasErrors(){

        return count(static::$instance->errors) > 0;
    }

    /**
     *
     *
     *
     * @param array $data
     * @param array $fieldRules
     *
     * @return array 
     * @throws Exception
     * @api
     */

    public static function check(array $data, array $fieldRules){ // $data should be the data from GET / POST / PUT / DELETE always...
         $valid = TRUE;
         $results = array();
         $callbacks = NULL;
         $fieldvalue = '';
         $pattern = '';

         // extract all callbacks

         foreach($fieldRules as $fieldname => $rule){

            static::$instance->allowed = NULL;
            static::$instance->fieldHasError = FALSE;

            $callbacks = array();
            $fieldvalue = '';
            $pattern = '';
            $limits = NULL;
            $boundary = '';
            $endIndex = -1;

            if(is_array($rule)){
               static::$instance->allowed = $rule['allowed'];
               $callbacks = explode("|", $rule['rule']);
               
               if(!is_array($callbacks)){
                  $callbacks = array();
               }

			         $callbacks[] = 'useAllowed';
            }else if(is_string($rule)){
               $_rule = $rule;
               $patternIndex = index_of($rule, '/');
               $maxMinIndex = index_of($rule, 'bounds:');


               if($patternIndex > -1){
                  $endIndex = index_of($rule, '|', $patternIndex);
                  if($endIndex == -1){
                     $endIndex = strlen($rule);
                  }
                  $pattern = substr($rule, $patternIndex, $endIndex-1);
                  $_rule = str_replace(('|'.$pattern), '', $_rule);
               }
               if($maxMinIndex > -1){
                  $endIndex = index_of($rule, '|', $maxMinIndex);
                  if($endIndex == -1){
                     $endIndex = (strlen($rule) - 1);
                  }
                  $boundary = substr($_rule, $maxMinIndex, $endIndex);
                  preg_match('/:([\d]+)?(\,[\d]+)?$/', $boundary, $limits);
                  static::$instace->bounds->max = $limits[1];
                  static::$instance->bounds->min = $limits[0];
                  $_rule = str_replace(('|'.$boundary), '', $_rule);
                  $boundary = 'bounds';
               }

               $callbacks = array_merge((array) $boundary, explode("|", $_rule));

            }else{

                throw new Exception("Validator: could not process ['". $rule['rule'] . "'] for $fieldname");
            }

            // setup callbacks to work on each field(s)
            foreach($callbacks as $callback){
               $fieldvalue = $data[$fieldname];
               $fieldvalue = trim(strip_tags(htmlspecialchars($fieldvalue, ENT_QUOTES, 'UTF_8')));
               
               if($callback == ''){ 
                   continue; 
               }

               $valid = $callback($fieldvalue, $fieldname, static::$instance, $pattern);

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
