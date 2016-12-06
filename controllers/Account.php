<?php 

class Account extends Controller {

        protected $params;

        public function __construct($params = array()){

             parent::__construct($params);
             
        }

        // @Override 

        public function index($models){

            # code ...
        }

        public function register($models){

            switch($this->params['mode']) {
                 case 'create':
                     # code...
                 break;
                 default:
                    return Response::view('register/index', array());
                 break;
            }
        }

        public function login($models){

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
                 default:
                     return Response::view('login/index', array());
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
