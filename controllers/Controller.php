<?php

class Controller {

	protected $params;

    public function __construct($params){

            $this->params = $params;
             
    }

    public function index($models){

        return Response::view('index', array('framework' => 'Jollof', 'title' => 'PHP MVC Framework'));	
    } 

}

?>