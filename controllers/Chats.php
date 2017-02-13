<?php

use Predis\Client;

class Chats extends Controller{
	
	protected $params = array();

	public function __construct($params = array()){

		 parent::__construct($params);
	}

	public function messaging($models){

		$input = Request::input()->getFields();

		$data = array(
			'event' => $input['type'],
			'data' => $input['message']
		);

		$redis = new Client('tcp://127.0.0.1:6379');
		$redis->publish('chat', json_encode($data));

		return Response::json($message);
	}
}