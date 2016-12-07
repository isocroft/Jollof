<?php
 
   /*!---------------------------------------------------------
    ! 
    ! In the begining, We have to boot up the {System} and then
    ! load up all class files needed to get started  using the
    ! Jollof autoloader.
    !
    !
    !
    ! * Jollof (c) Copyright 2016
    ! *
    ! *
    ! *
    ! *
    ! *
    -----------------------------------------------------------*/

    require_once __DIR__ . '/system/boot.php'; 

   /*!---------------------------------------------------------
    ! 
    ! Create the most important object in this framework. Here
    ! we are initializing the core of the framework where all
    ! functionality resides {$app}.
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

    $app = new \Providers\Core\App;

   /*!--------------------------------------------------------
    ! 
    ! Create the 2 necessary services we need to get our
    ! application to function properly. The Database and
    ! Environment settings are loaded and processed here.
    !
    !
    ! *
    ! *
    ! *
    ! *
    ! *
    ! *
    --------------------------------------------------------*/

    $app->installDBService(require __DIR__ . '/configs/db.php');
    $app->installENVService(require __DIR__ . '/configs/env.php');

   /*!------------------------------------------------------
    ! 
    ! It's now time to make all custom ENV variables available 
    ! to every part of the application by exposing the {$env}
    ! variable
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
    $env = $app->exposeEnvironment(basename(__DIR__));

   /*!------------------------------------------------------
    ! 
    ! Instantiate all necessary components needed by the app
    ! internals e.g Routes, Controllers, Models and Views
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
    $app->registerCoreComponents();

?>