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

 Router::bind('/admin', array('models' => array('Todo', 'TodoList')));

 Router::bind('/account/login/', array('models' => array()));

 Router::bind('/account/register/', array('models' => array()));

 Router::bind('/account/signin/@provider', array('verb'=> 'post', 'models' => array('User', 'UserRole', 'UserThrottle'), 'params'=> array('provider' => '/^(?:(?:oauth-([a-z]+))|email)$/i')));

 Router::bind('/account/signup/@mode', array('verb' => 'post', 'models' => array('User', 'UserRole', 'UserThrottle'), 'params' => array('mode' => '/^(create)$/i')));

