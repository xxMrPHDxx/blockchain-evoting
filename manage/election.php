<link rel="stylesheet" href="css/elections.css">
<?php
$function = isset($_GET['function']) ? $_GET['function'] : null;

$success = null; $error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if($function == 'add'){
		extract($_POST);
		$result = $conn->query(
			"INSERT INTO elections (name, due_date) VALUE ('$name', '$due_date')"
		);
		if($result) $success = "Election added successfully!";
		else $error = "Failed to add election!";
	}
}

if($function == 'add'){
?>
<form method="POST" enctype="multipart/form-data">
	<input type="text" name="name" placeholder="Election name">
	<input type="date" name="due_date" placeholder="Due date">
	<input type="submit" value="Add Election">
</form>
<?php
}else{
	$elections = $conn->query("SELECT * FROM elections");
?>
<div id="elections">
<?php
	while($row = $elections->fetch_assoc()){
?>
	<div class="election">
		<span>Name: <?php echo $row['name'] ?></span>
		<span>Due date: <?php echo $row['due_date'] ?></span>
		<a href="?page=manage&type=candidate&election=<?php echo $row['id'] ?>">
			<button>Candidates</button>
		</a>
	</div>
<?php
	}
?>
</div>
<a href="?page=manage&type=election&function=add"><button>New</button></a>
<?php
}

if(($success || $error) && !($success && $error)){
?>
	<div class="toast <?php $error?'error':'success' ?>">
		<?php echo $error ? $error : $success; ?>
	</div>
<?php
}
?>
