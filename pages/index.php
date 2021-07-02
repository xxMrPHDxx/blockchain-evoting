<?php
$res = $conn->query("SELECT * FROM voters WHERE username='admin'");
if($res){
	echo "<span>".base64_encode($res->fetch_assoc()['public_key'])."</span>";
}
?>
