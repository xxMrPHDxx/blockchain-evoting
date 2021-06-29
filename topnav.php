<?php
if(isset($_GET['action'])){
	if($_GET['action']){ session_destroy(); header('Location: login.php'); }
}
?>
<div id="topnav">
	<a href="?action=logout">Logout</a>
</div>
