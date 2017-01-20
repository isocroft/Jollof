<?php

/*!
 * Jollof (c) Copyright
 *
 *
 * {Controller.php}
 *
 */

class Controller {

	protected $params = array();

    public function __construct(array $params = array()){

            $this->params = $params;

    }

    public function index($models){

        return Response::view('index', array('framework' => 'Jollof', 'title' => 'PHP MVC Framework'));
    }

}

?>