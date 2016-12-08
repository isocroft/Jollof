<?php

/*!------------------------------------------------------
! 
! This is the routes file for configuring all route urls
! and their corresponding HTTP verb, models and route
! parameters.
!
!
! * Jollof (c) Copyright 2016
! *
! *
! * {setup.php}
! *
! *
----------------------------------------------------------*/
 
 Router::bind('/', array('models' => array()));

 Router::bind('/admin', array('models' => array()));
 
 Router::bind('/account/login/@provider', array('models' => array(), 'params'=>'/^(?:(?:oauth-([a-z]+))|email|)$/i'));

 Router::bind('/account/register/@mode', array('models' => array(), 'params' => array('/^(create|)$/i')));

 
