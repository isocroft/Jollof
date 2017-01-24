<?php

    define('JOLLOF_EXEC_ID', mt_rand(1, time()));

   /*!------------------------------------------------------
    ! It's only natural that we rep Naija by modifiying the
    ! default timezone for this server. Feel free to change
    ! it anytime
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
    date_default_timezone_set('Africa/Lagos'); # UTC

  /*!------------------------------------------------------
    !
    ! Include the main application file so we can get this
    ! show on the road ;)
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
    require_once __DIR__ . '/../app.php';


    /*!------------------------------------------------------
    !
    ! Load up all validation rules for request input validation
    !
    !
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
    require_once __DIR__ . '/../system/extensions/validation/rules/setup.php';


    /*!------------------------------------------------------
    !
    ! Load up our routes file so we know what the client-side
    ! is asking for and reply accordingly
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
	require_once __DIR__ . '/../system/routes/setup.php';


   /*!------------------------------------------------------
    !
    ! Load up all the application middlewares
    !
    !
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
    require_once __DIR__ . '/../system/middlewares/setup.php';


   /*!------------------------------------------------------
    !
    ! Load up all the application event observers/callbacks
    !
    !
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
    require_once __DIR__ . '/../system/events/setup.php';


   /*!------------------------------------------------------
    !
    ! Start piecing together the controller action and models
    ! from the current activated route
    !
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
    $app->initHTTPResolver();

   /*!------------------------------------------------------
    !
    ! Lets' cross our fingers and have a good ride on the
    ! application cycle. Osheee!
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


?>
