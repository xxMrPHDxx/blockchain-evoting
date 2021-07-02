<?php
	if(isset($_POST['vote_id'])){
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

		$vote = $conn->query("SELECT * FROM votes WHERE id=".$_POST['vote_id']);
		if(!$vote || $vote->num_rows != 1){
			$response['success'] = false;
			$response['error'] = 'Vote does not exist!';
			goto end;
		}
		$vote = $vote->fetch_assoc();

		// Check if this vote has already been signed or not
		if($vote['signature'] != null){
			$response['success'] = false;
			$response['error'] = 'Vote has already been signed!';
			goto end;
		}

		// Getting the keypair
		$keypair = new KeyPair(
			$voter['private_key'],
			$voter['public_key'],
			$voter['cipher'],
			$voter['iv']
		);

		// Signing the vote
		$sign = $conn->query(
			"UPDATE votes SET signature='".
			base64_encode($keypair->sign(json_encode($vote))).
			"'"
		);
		if(!$sign){
			$response['success'] = false;
			$response['error'] = 'Failure during signing!';
			goto end;
		}
		$response['message'] = 'Vote has been signed successfully!';
	}
	end:
?>
