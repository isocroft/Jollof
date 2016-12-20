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

            $validInputs = Validator::check( $inputs, 
                array(
                    'email' => "email|required",
                    'password' => "password|required|bounds:8",
                    'first_name' => 'name|required',
                    'last_name' => 'name|required',
                    'mobile' => 'mobile_number|required'
                )
            );

            $json = array('status' => 'ok', 'result' => NULL);

            switch($this->params['mode']) {
                 case 'create':
                     if(Validator::hasErrors()){
                          $json['status'] = 'error';
                          $json['result'] = Validator::getErrors();
                     }else{

                        Auth::createNewContext( $models );
                        $json['result'] = Auth::register( $validInputs );
                     }
                 break;
            }

            return Response::json( $json );
        }

        public function login($models){

             
            return Response::view('login/index', array('framework' => 'Jollof', 'title' => 'Login'));
            
        }

        public function signin($models){

            $inputs = Request::input()->getFields();

            $validInputs = Validator::check( $inputs, 
                array(
                    'email' => "email|required",
                    'password' => "password|required"
                )
            );

            $json = array( 'status' => 'ok', 'result' => NULL );

            switch($this->params['provider']) {
                 case 'oauth-facebook':
                     # code...
                 break;
                 case 'oauth-instagram':
                     # code...
                 break;
                 case 'email':
                    if(Validator::hasErrors()){
                        $json['status'] = 'error';
                        $json['result'] = Validator::getErrors();
                    }else{
                        Auth::createNewContext( $models );
                        $json['result'] = Auth::login( $validInputs );
                    }
                 break;
             }

             return Response::json( $json );
        }

        public function logout($models){
             
            # code ...
        }

        public function passwordreset($models){

            # code ...
        }

        public function activate($models){

            # code ...
        }
}

?>
