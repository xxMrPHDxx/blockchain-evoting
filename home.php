<?php
session_start();
if(!isset($_SESSION['id'])) header('Location: login.php');

// Check if this user has a private key or not, if not create one
include_once 'encrypt.php';
include_once 'db_connect.php';
$check = $conn->query("SELECT * FROM voters WHERE id=".$_SESSION['id']);
$check = $check->fetch_assoc();
if($check['public_key'] == null){
	$key = new KeyPair();
	$conn->query(
		"UPDATE voters SET public_key='".$key->get_key().
		"', cipher='".$key->get_cipher().
		"', iv='".$key->get_iv().
		"' WHERE id=".$_SESSION['id']
	);
}else{
	$key = new KeyPair(
		$check['public_key'],
		$check['cipher'],
		$check['iv']
	);
}

// Determine the content page to show.
// If it doesn't exist, go to index page
if(!isset($_GET['page'])) $_GET['page'] = 'index';
$page = $_GET['page'];
if(!file_exists("pages/$page.php")){
	if($page == 'index') die('404 Not Found: Failed to load index page!');
	header('Location: home.php');
}
?>
<html>
    <head>
				<meta charset="utf-8"/>
				<link rel="stylesheet" href="css/default.css"/>
				<link rel="stylesheet" href="css/home.css"/>
				<link rel="stylesheet" href="css/<?php echo $page ?>.css"/>
				<script
			  src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    </head>
		<body class="main<?php echo $_SESSION['is_admin']?' admin':'' ?>">
				<?php include 'topnav.php' ?>
				<?php if($_SESSION['is_admin']) include 'sidebar.php' ?>
				<div id="content">
					<?php 
						include_once "pages/$page.php";
					?>
				</div>
    </body>
</html>
