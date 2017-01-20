<?php

// There's no way the below can be a complete whitelist - just to be clear ;)
$real_url_endings = array(
	'jpg',
	'png',
	'jpeg',
	'js',
	'zip',
	'css',
	'json',
	'xml',
	'html',
	'gif'
);

$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$ext = pathinfo($path, PATHINFO_EXTENSION);

if(in_array($ext, $real_url_endings)
	|| ($path !== '/' && file_exists($_SERVER['DOCUMENT_ROOT'] . ltrim($path, "/")))){
	// let the PHP server handle this
	return false;
}else{
	if($ext === "php"){
		if(function_exists('http_response_code')){
			http_response_code(403);
		}else{
			header(sprintf('%s %s %s',$_SERVER['SERVER_PROTOCOL'], '403', 'Forbidden'));
		}
		echo "</h2>Jollof - Forbidden</h2>";
		exit;
	}

	/*file_put_contents("php://stdout", sprintf("[%s] %s:%s [%s]: %sn", date("D M j H:i:s Y"), $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'], "", $_SERVER['REQUEST_URI']));*/

	require_once __DIR__ . '/public/index.php';
}

?>