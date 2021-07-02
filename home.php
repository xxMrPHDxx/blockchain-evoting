<?php
session_start();
if(!isset($_SESSION['id'])) header('Location: login.php');

include 'check_and_generate_key.php';

// Determine the content page to show.
// If it doesn't exist, go to index page
if(!isset($_GET['page'])) $_GET['page'] = 'index';
$page = $_SESSION['is_admin'] ? $_GET['page'] : 'voting';
if(!file_exists("pages/$page.php")){
	if($page == 'index' || $page == 'voting')
		die("404 Not Found: Failed to load $page page!");
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
