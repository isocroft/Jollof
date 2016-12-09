<?php 

  /*!
   * Jollof (c) Copyright 2016
   * 
   * {setup.php}
   *
   */

  function email(&$value, $fieldname, $pattern, $validator){
	     trim($value);
	     $valid = $validator->filterEmail($value);
		   if($valid === FALSE){
		     return "The is not a valid email address for '$fieldname'"; 
		   }
		   return $valid;
		
  }
	
  function useAllowed($value, $fieldname, $pattern, $validator){
       if(is_null($validator->allowed)){
  	       return "Field options not accessible for '$fieldname'";
       }
       $valid = in_array($value, $validator->allowed);
       return ($valid === FALSE)? "This '$value' is invalid for '$fieldname'" : $valid;
  }

  function required(&$value, $fieldname, $pattern, $validator){
        $valid = !empty($value);
    		if($valid === FALSE){
    		  return "Field '$fieldname' is required";
    		}
    		return $valid;
  }

  function password(&$value, $fieldname, $pattern, $validator){
         $value = trim($value);
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid password";
         }
         return $valid;
  }

  function name(&$value, $fieldname, $pattern, $validator){
         $value = trim($value);
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid '$fieldname'";
         }
         return $valid;
  }

  function mobile_number(&$value, $fieldname, $pattern, $validator){
         $value = trim($value);
         $valid = (bool) preg_match($pattern, $value);
         if($valid === FALSE){
            return "This is not a valid '$fieldname'";
         }
         return $valid;
  }

  /* ADD YOUR CUSTOM VALIDATOR RULES (functions) BELOW */

  function mail_box(&$value, $fieldname, $pattern, $validator){

        # code ...
  }


?>
