<?php
// Check if this user has a private key or not, if not create one
include_once 'keypair.php';
include_once 'db_connect.php';
$check = $conn->query("SELECT * FROM voters WHERE id=".$_SESSION['id']);
$check = $check->fetch_assoc();
if(!isset($check['public_key']) ||
	 !isset($check['private_key']) ||
	 !isset($check['cipher']) ||
	 !isset($check['iv'])){
	$key = new KeyPair();
	$conn->query(
		"UPDATE voters SET ".
		"private_key='".$key->get_private()."', ".
		"public_key='".$key->get_public()."', ".
		"cipher='".$key->get_cipher()."', ".
		"iv='".$key->get_iv()."' ".
		"WHERE id=".$_SESSION['id']
	);
}else{
	$key = new KeyPair(
		$check['private_key'],
		$check['public_key'],
		$check['cipher'],
		$check['iv']
	);
}
?>
