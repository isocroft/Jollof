<?php 

class Account extends Controller {

        protected $params = array();

        public function __construct(array $params = array()){

             parent::__construct($params);
             
        }

        // @Override 

        public function index($models){

            # code ...
        }

        public function register($models){

            return Response::view('register/index', array('framework' => 'Jollof', 'title' => 'Register'));
        }

        public function signup($models){

            $inputs = Request::input()->getFields();

            $validInputs = Validator::checkAndSanitize( $inputs, 
                array(
                    'email' => "email|required",
                    'password' => "password|required|/^(?:[^\t\r\n\f\b\~\"\']+)$/i",
                    'first_name' => 'name|required|/^(?:[^\S\d\t\r\n]+)$/i',
                    'last_name' => 'name|required|/^(?:[^\S\d\t\r\n]+)$/i',
                    'mobile' => 'mobile_number|required|/^(?:070|071|081|080|090|091)(?:\d{8})$/'
                )
            );

            $validateErrors = Validator::getErrors();

            if(count($validateErrors) > 0){
                return Response::json(array('status' => 'error', 'result' => $validateErrors));
            }

            switch($this->params['mode']) {
                 case 'create':
                     # code...
                 break;
            }

            return Response::json(array('status' => 'ok'));
        }

        public function login($models){

             
            return Response::view('login/index', array('framework' => 'Jollof', 'title' => 'Login'));
            
        }

        public function signin($models){

            $inputs = Request::input()->getFields();

            $validInputs = Validator::checkAndSanitize( $inputs, 
                array(
                    'email' => "email|required",
                    'password' => "password|required|/^(?:[^\t\r\n\f\b\~\"\']+)$/i"
                )
            );

            $validateErrors = Validator::getErrors();

            switch($this->params['provider']) {
                 case 'oauth-facebook':
                     # code...
                 break;
                 case 'oauth-instagram':
                     # code...
                 break;
                 case 'email':
                     # code...
                 break;
             }
        }

        public function logout($models){
             
            # code ...
        }

        public function passwordreset($models){

            # code ...
        }

        public function activation($models){

            # code ...
        }
}

?>
