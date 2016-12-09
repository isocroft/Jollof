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
 
 Router::bind('/', array('verb' => 'get', 'models' => array()));

 Router::bind('/admin', array('verb' => 'get', 'models' => array()));

 Router::bind('/account/login/@provider', array('verb' => 'get', 'models' => array(), 'params'=>array('provider' => '/^$/i')));

 Router::bind('/account/register/@mode', array('verb' => 'get', 'models' => array(), 'params' => array('mode' => '/^$/i')));
 
 Router::bind('/account/login/@provider', array('verb' => 'post', 'models' => array(), 'params'=> array('provider' => '/^(?:(?:oauth-([a-z]+))|email)$/i')));

 Router::bind('/account/register/@mode', array('verb' => 'post', 'models' => array(), 'params' => array('mode' => '/^create$/i')));

 
