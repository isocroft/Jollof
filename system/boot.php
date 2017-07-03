<?php

  /*!------------------------------------------------------
    ! 
    ! Starting off, We need to include the Composer class
    ! loader to allow us use Composer packages/modules only 
    ! if at least one of the packages/modules is available.
    !
    !
    ! * Jollof (c) Copyright 2016 - 2017
    ! *
    ! *
    ! *
    ! *
    ! *
    --------------------------------------------------------*/
    
    if(file_exists(__DIR__ . '/../packages/vendor/autoload.php')){
         require __DIR__ . '/../packages/vendor/autoload.php';
    }   

  /*!----------------------------------------------------------
    ! 
    ! Load in all basic functions that help out with reusable
    ! functionality  
    !
    !
    !
    ! *
    ! *
    ! *
    ! *
    ! *
    ! *
    ------------------------------------------------------------*/

    require __DIR__ . '/base/functions.php';  

   /*!------------------------------------------------------
    ! 
    ! Lets' continue by including the Jollof class loader 
    ! from its location to load all component / model /
    ! controller class files  
    !
    !
    ! *
    ! *
    ! *
    ! *
    ! *
    ! *
    --------------------------------------------------------*/

    require_once __DIR__ . '/base/ClassLoader.php';

  /*!------------------------------------------------------
    ! 
    ! Instantiate the class loader and register all classes
    ! via their corresponding class paths
    !
    !
    !
    ! *
    ! *
    ! *
    ! *
    ! *
    ! *
    --------------------------------------------------------*/

	if(!array_key_exists('loader', get_defined_vars())) {

    	   $loader = new ClassLoader(json_decode(file_get_contents(__DIR__ . "/load.json"), TRUE));
    	   $loader->addClassMap(require __DIR__ . '/base/class_maps.php');
    	   $loader->register(true);
	}

    /*!--------------------------------------------------------
    ! 
    ! Otherwise, if something goes awry, kill it process fast 
    ! ^Naija Style^ 
    !
    !
    !
    ! *
    ! *
    ! *
    ! *
    ! *
    ! *
    ----------------------------------------------------------*/

	else { 
	
        die('ClassLoader processing failed - Jollof terminated abruptly');
		
    } 
	
?>
