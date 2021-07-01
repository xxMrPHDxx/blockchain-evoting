<?php
if(!isset($_GET['election']))
	header('Location: home.php?page=manage&type=election');
$election = $_GET['election'];

$function = isset($_GET['function']) ? $_GET['function'] : null;

$success = null; $error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if($function == 'add' &&
		isset($_POST['position']) &&
		isset($_POST['freq']) &&
		$_POST['freq'] >= 1){
		$election = $conn->real_escape_string($election);
		$position = $conn->real_escape_string($_POST['position']);
		$freq = $conn->real_escape_string($_POST['freq']);

		// Check if the election and position setting has already been added or not
		$check = $conn->query("SELECT * FROM election_settings WHERE election_id=$election AND position_id=$position");
		if(!$check){
			$error = 'Failed to set election settings!';
			goto ignore;
		}

		if($check->num_rows == 0){
			// Setting doesn't exists
			$result = $conn->query("INSERT INTO election_settings (election_id, position_id, frequency) VALUE ($election, $position, $freq)");
			if($result) $success = 'Setting added successfully';
		}else{
			// Setting already exists so we just need to update it
			$id = $check->fetch_assoc()['id'];
			$result = $conn->query("UPDATE election_settings SET frequency=$freq WHERE id=$id");
			if($result) $success = 'Setting updated successfully';
		}
	}
}

ignore:
if($function == 'add'){
?>
<form method="POST" enctype="multipart/form-data">
	<label for="position">Position:&nbsp;</label>
	<select name="position">
	<?php
	$elections = $conn->query("SELECT * FROM positions");
	while($row = $elections->fetch_assoc()){
		extract($row);
	?>
		<option value="<?php echo $id ?>"><?php echo $name ?></option>
	<?php
	}
	?>
	</select/>
	<input type="number" name="freq" min="1" placeholder="Target frequency">
	<input type="submit" value="Set">
</form>
<?php
}else{
?>
<h2>Current Settings</h2>
<hr>
<div id="settings">
<?php
	$settings = $conn->query(
		"SELECT *, e.id AS e_id, p.id AS p_id, ".
			"e.name AS election, p.name AS position ".
			"FROM election_settings s ".
			"JOIN elections e ON s.election_id=e.id ".
			"JOIN positions p ON s.position_id=p.id ".
			"WHERE election_id=$election");
	while($row = $settings->fetch_assoc()){
?>
	<div class="setting">
		<span>Election: <?php echo $row['election'] ?></span>
		<span>Position: <?php echo $row['position'] ?></span>
		<span>Target frequency: <?php echo $row['frequency'] ?></span>
	</div>
<?php
	}
?>
</div>
<?php
}
?>
	<a href="?page=manage&type=setting&election=<?php echo $election ?>&function=add">
	<button>New</button>
</a>
<?php
if(($success || $error) && !($success && $error)){
?>
	<div class="toast <?php $error ? 'error' : 'success'?>">
		<?php echo $error ? $error : $success ?>
	</div>
<?php
}
?>
