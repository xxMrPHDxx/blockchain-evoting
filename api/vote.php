<?php
if(isset($_POST['candidate']) && isset($_POST['election'])){
	foreach($_POST as $key=>$value)
		$$key = $conn->real_escape_string($value);

	// Getting the voter info
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

	// Check if vote already exist and has already been signed.
	// If it has been signed, it should have been added to the 
	// blockchain by the blockchain queueing server
	$exist = $conn->query(
		"SELECT * FROM votes WHERE election_id=$election".
		" AND candidate_id=$candidate AND voter_id=".$voter['id']
	);
	if($exist && $exist->num_rows != 0){
		$response['success'] = false;
		$response['message'] = 'Vote already exists!';
		goto end;
	}
	if($exist && $exist->num_rows != 0 && $exist->fetch_assoc()['signature']){
		$response['success'] = false;
		$response['message'] = 'Vote has already been signed!';
		goto end;
	}
	
	// Check if the user has voted enough candidates for the given rank/position
	$target = $conn->query(
		"SELECT SUM(frequency) AS total FROM election_settings".
		" WHERE election_id=$election"
	)->fetch_assoc();
	$actual = $conn->query(
		"SELECT COUNT(*) AS total FROM votes".
		" WHERE voter_id=".$voter['id']." AND election_id=$election"
	)->fetch_assoc();
	if($actual['total'] >= $target['total']){
		$response['success'] = false;
		$response['message'] = 'Total votes by this user exceeds the target count!';
		goto end;
	}

	// Insert the vote
	$insert = $conn->query(
		"INSERT INTO votes (voter_id, candidate_id, election_id) VALUE ".
		"(".$voter['id'].", $candidate, $election)"
	);
	if(!$insert){
		$response['success'] = false;
		$response['message'] = 'Failed to vote!';
		goto end;
	}

	// Obtain the vote id we just inserted
	$vote = $conn->query(
		"SELECT id FROM votes ".
		"WHERE voter_id=".$voter['id'].
		" AND candidate_id=$candidate AND election_id=$election"
	);
	$vote = $vote->fetch_assoc();

	$response['vote_id'] = $vote['id'];
}
else $response['success'] = false;
end:
?>
