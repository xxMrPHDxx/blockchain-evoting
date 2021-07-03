<?php
if(isset($_POST['data'])){
	// Getting the user info
	$voter = $conn->query(
		"SELECT * FROM voters WHERE public_key='".
		base64_decode($_HEADERS['Authorization']).
		"'"
	);
	if(!$voter || $voter->num_rows != 1){
		$response['success'] = false;
		$response['error'] = 'User does not exist!';
		goto end;
	}
	$voter = $voter->fetch_assoc();

	// Creating the keypair for decryption
	$keypair = new KeyPair(
		$voter['private_key'],
		$voter['public_key'],
		$voter['cipher'],
		$voter['iv']
	);

	// Send the decrypted data
	$response['data'] = $keypair->decrypt($_POST['data']);
}
else $response['success'] = false;
end:
?>
