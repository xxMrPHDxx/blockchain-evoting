<link rel="stylesheet" href="css/candidate.css"/>
<?php
if(!isset($_GET['election']))
	header('Location: home.php?page=manage&type=election');
$election = $_GET['election'];
$function = isset($_GET['function']) ? $_GET['function'] : null;

$success = null; $error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if($function == 'add' && 
			isset($_POST['name']) && !empty($_POST['name']) &&
			isset($_FILES['image']) && 
			isset($_POST['position']) &&
			isset($_POST['election'])){
		extract($_FILES['image']);
		$size = getimagesize($tmp_name);
		if($size[0] > 300 || $size[1] > 300)
			$error = 'Image too big!';
		elseif($size[0] > $size[1])
			$error = 'Image is not in portrait mode!';
		elseif($size > 0){
			$name = $conn->real_escape_string($_POST['name']);
			$image = base64_encode(file_get_contents($tmp_name));
			$position = $conn->real_escape_string($_POST['position']);
			$election = $conn->real_escape_string($_POST['election']);
			$result = $conn->query(
				"INSERT INTO candidates (name, image, position_id, election_id) VALUE ('$name', '$image', $position, $election)"
			);
			if($result) $success = 'Candidate added successfully!';
			else $error = 'Failed to add candidate!';
		}
	}
}
if($function == 'add'){
?>
<form id="add-candidate" method="POST" enctype="multipart/form-data">
	<input type="file" accept="image/*" name="image">
	<input type="text" name="name" placeholder="Candidate Name" value="Luqman">
	<select name="election">
	<?php
	$result = $conn->query("SELECT * FROM elections");
	for($i=0; $i<$result->num_rows; $i++){
		extract($result->fetch_assoc());
	?>
		<option value="<?php echo $id ?>"><?php echo $name ?></option>
	<?php
	}
	?>
	</select>
	<select name="position">
	<?php
	$result = $conn->query("SELECT * FROM positions");
	for($i=0; $i<$result->num_rows; $i++){
		extract($result->fetch_assoc());
	?>
		<option value="<?php echo $id ?>"><?php echo $name ?></option>
	<?php
	}
	?>
	</select>
	<input type="submit" value="Add Candidate">
</form>
<?php 
}else{
?>
<div id="positions">
<?php
	$settings = $conn->query(
		"SELECT position_id, p.name AS position FROM election_settings s ".
		"JOIN positions p ON s.position_id=p.id ".
		"WHERE election_id=$election"
	);
	while($settings && ($setting = $settings->fetch_assoc())){
		$candidates = $conn->query(
			"SELECT * FROM candidates WHERE position_id=".$setting['position_id'].
			" AND election_id=$election"
		);
		if(!$candidates || $candidates->num_rows == 0) continue;
?>
	<div class="position">
		<h3><?php echo $setting['position'] ?></h3>
		<div id="candidates">
<?php
		while($row = $candidates->fetch_assoc()){
?>
			<div class="candidate">
				<img src="data:image/*;base64,<?php echo $row['image'] ?>"></img>
				<span class="name"><?php echo $row['name'] ?></span>
			</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>
</div>
<button onclick="location+='&function=add'">New</button>
<?php
}

if(($error || $success) && !($error && $success)){ ?>
<div class="toast <?php $error?'error':'success' ?>">
	<?php echo $error ? $error : $success ?>
</div>
<?php } ?>
