<?php

class Account extends Controller {

        protected $params = array();

        public function __construct(array $params = array()){

             parent::__construct($params);

        }

        // @Override

        public function index($models){

            return Response::text('You are not loggged in', 403);
        }

        public function register($models){

            return Response::view('register/index', array('framework' => 'Jollof', 'title' => 'Register'));
        }

        public function signup($models){

            # code ...
        }

        public function login($models){


            return Response::view('login/index', array('framework' => 'Jollof', 'title' => 'Login'));

        }

        public function signin($models){

            # code ...
        }

        public function logout($models){

            # code ...
        }

        public function reset_password($models){

            # code ...
        }

        public function activate($models){

            # code ...
        }

        public function deactivate($models){

            # code ...
        }

        public function forced_logout($models){

            $user = Auth::user();

            if($user === NULL){

                return Response::view('forcedlogout/index', array('user' => array()));
            }

            return Response::view('forcedlogout/index', array('user' => $user));
        }
}

?>
