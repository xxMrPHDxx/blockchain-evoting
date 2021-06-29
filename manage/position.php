<link rel="stylesheet" href="css/position.css">
<?php
$function = isset($_GET['function']) ? $_GET['function'] : null;

$success = null; $error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if($function == 'add' &&
		$_POST['name']){
		$name = $conn->real_escape_string($_POST['name']);
		$result = $conn->query("INSERT INTO positions (name) VALUE ('$name')");
		if($result) $success = 'Position added successfully!';
		else $error = 'Failed to add position';
	}
}

if($function == 'add'){
?>
<form method="POST" enctype="multipart/form-data">
	<input type="text" name="name" placeholder="Position name">
	<input type="submit" value="Add">
</form>
<?php
}else{
	$positions = $conn->query("SELECT * FROM positions");
	while($row = $positions->fetch_assoc()){
		extract($row);
?>
	<div class="position">
		<span>Name: <?php echo $row['name'] ?></span>
		<a href="?page=manage&type=position&function=edit&position=<?php echo $id ?>">
			<button>Edit</button>
		</a>
	</div>
<?php
	}
?>
<a href="?page=manage&type=position&function=add"><button>New</button></a>
<?php
}
?>
