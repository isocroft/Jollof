<?php

class Account extends Controller {

        protected $params = array();

        public function __construct(array $params = array()){

             parent::__construct($params);

        }

        // @Override

        public function index(){

            return Response::text('You are not loggged in', 403);
        }

        public function register(){

            return Response::view('register/index', array('framework' => 'Jollof', 'title' => 'Register'));
        }

        public function signup(){

            # code ...
        }

        public function login(){


            return Response::view('login/index', array('framework' => 'Jollof', 'title' => 'Login'));

        }

        public function signin(){

            # code ...
        }

        public function logout(){

            # code ...
        }

        public function reset_password(){

            # code ...
        }

        public function activate(){

            # code ...
        }

        public function deactivate(){

            # code ...
        }

        public function forced_logout(){

            $user = Auth::user();

            if($user === NULL){

                return Response::view('forcedlogout/index', array('user' => array()));
            }

            return Response::view('forcedlogout/index', array('user' => $user));
        }
}

?>
