<?php
if(!isset($_GET['type'])) header('Location: home.php');
$type = $_GET['type'];
if(!file_exists("manage/$type.php")) header('Location: home.php');
include_once "manage/$type.php";
?>
