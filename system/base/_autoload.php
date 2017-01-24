<?php

function __autoload($class){ // __autoload is supported in PHP 5.0.0 till PHP 5.2.0

       $BASE_DIR = __DIR__;

       if(!isset($CLASSMAP)){

       		return;
       }

       foreach($CLASSMAP as $className => $classPath){
       	    if(substr($className, 1) == $class){ // in PHP 5.0.0 till PHP 5.2.0, the leading backward slash is always missing!!
	       	   if(file_exists("{$BASE_DIR}{$classPath}.php")){
	       	       include "{$BASE_DIR}{$classPath}.php";

		           return hasClassBeenAutoloaded($class);
	       	    }
	       	}
       }

       return hasClassBeenAutoloaded($class);
}


function hasClassBeenAutoloaded($class){
    	return class_exists($class);
}

?>