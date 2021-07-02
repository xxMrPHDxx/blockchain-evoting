<?php
include_once 'db_connect.php';
include_once 'keypair.php';
	
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json');

$_HEADERS = getallheaders();
global $response;
$response = array('success'=>true);

if(isset($_HEADERS['Authorization'])){
	if(!isset($_GET['action']) || !file_exists('api/'.$_GET['action'].'.php')){
		$response['success'] = false;
		$response['error'] = 'Invalid action specified!';
	}else{
		$action = $_GET['action'];
		include "api/$action.php";
	}
}else{
	$response['success'] = false;
	$response['error'] = 'You need to be authorized to use this!';
}

die(json_encode($response));
?>
