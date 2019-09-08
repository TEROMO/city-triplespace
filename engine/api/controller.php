<?php 
	header('Content-Type: application/json');
	require 'API.php';
	$api = new \API\API($routeInfo[2]['page']);
	echo json_encode($api->action(), JSON_UNESCAPED_UNICODE);
?>