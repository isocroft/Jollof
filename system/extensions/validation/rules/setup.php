<?php

  /*!
   * Jollof (c) Copyright 2016
   *
   * {setup.php}
   *
   */

  /*!
   * @TODO: This separate functions for
   *
   * -- This will be the new implementation format as of v1.0.0 for validation rules.
   *
   *  Validator::addRule('email', function($fieldObject, $validator){
   *
   *        # $fieldObject will be an instance of (stdClass)
   *
   *        $value = $fieldObject->value;
   *        $name = $fieldObject->name;
   *        $pattern = $fieldObject->pattern;
   *
   *        return $validator->response($validator->filterEmail($value));
   * });
   *
   *
   *
   */

  function email($value, $fieldname, $validator, $pattern = ''){
	     $valid = $validator->filterEmail($value);
		   if($valid === FALSE){
		      return "This is not a valid '{$fieldname}'";
		   }
		   return $valid;

  }

  function useAllowed($value, $fieldname, $validator, $pattern = ''){
       if(is_null($validator->allowed)){
  	       return "Field options not accessible for '{$fieldname}'";
       }
       $valid = in_array($value, $validator->allowed);
       return ($valid === FALSE)? "This is an invalid value for '$fieldname'" : $valid;
  }

  function required($value, $fieldname, $validator, $pattern = ''){
        $valid = !empty($value);
    		if($valid === FALSE){
    		  return "'{$fieldname}' is required";
    		}
    		return $valid;
  }

  function bounds($value, $fieldname, $validator, $pattern = ''){
      $len = strlen($value);
      $max = isset($validator->bounds->max)? $validator->bounds->max : $len;
      $min = isset($validator->bounds->min)? $validator->bounds->min : 0;
      $valid = (($len >= $min) && ($len <= $max));

      if($valid === FALSE){
          return "'{$fieldname}' is out of bounds";
      }

      return $valid;
  }

  function password($value, $fieldname, $validator, $pattern = '/^(?:[^\t\r\n\f\b\~\"\']+)$/i'){
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid password";
         }
         return $valid;
  }

  function name($value, $fieldname, $validator, $pattern = '/^(?:[^\S\d\t\r\n]+)$/i'){
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid '{$fieldname}'";
         }
         return $valid;
  }

  function mobile_number($value, $fieldname, $validator, $pattern = '/^(?:070|071|081|080|090|091)(?:\d{8})$/'){
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid '{$fieldname}'";
         }
         return $valid;
  }

  /* ADD YOUR CUSTOM VALIDATOR RULES (functions) BELOW */

  function zip_code($value, $fieldname, $validator, $pattern = '/^(.)*$/'){

        # code ...
  }


?>
