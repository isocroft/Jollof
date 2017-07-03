 <?php

 class Admin extends Controller {

	 protected $params;

	 public function __construct(array $params = array()){

		 parent::__construct($params);

	 }

	 public function index(){

		 return Response::view('admin/index', array());

	 }

 } ?>