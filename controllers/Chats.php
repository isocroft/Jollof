<?php

use Predis\Client as Client;

class Chats extends Controller{
	
	protected $params = array();

	public function __construct($params = array()){

		 parent::__construct($params);
	}

	public function messaging(){

		$input = Request::input()->getFields();

		$data = array(
			'event' => $input['type'],
			'data' => $input['message']
		);

		/* Redis: default setup for development [host/port]  */
		$redis = new Client('tcp://127.0.0.1:6379');
		$redis->publish('chat', json_encode($data));

		return Response::json($message);
	}
}