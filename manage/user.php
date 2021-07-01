<?php
$function = isset($_GET['function']) ? $_GET['function'] : null;

$success = null; $error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if($function == 'add' &&
			isset($_POST['name']) &&
			isset($_POST['user']) &&
			isset($_POST['pass']) && isset($_POST['rpass']) &&
			$_POST['pass'] == $_POST['rpass']){
		foreach($_POST as $key=>$value){
			$$key = $conn->real_escape_string($value);
		}
		$pass = hash('sha256', $pass);
		$admin = isset($_POST['admin'])?1:0;
		$keypair = new KeyPair();
		$pub_key = $keypair->get_key();
		$cipher = $keypair->get_cipher();
		$iv = $keypair->get_iv();

		// Check if username already exists
		$check = $conn->query("SELECT * FROM voters WHERE username=$user");
		if($check && $check->num_rows != 0){
			$error = 'Username already exists!';
			goto ignore;
		}

		$result = $conn->query("INSERT INTO voters ".
			"(name, username, password, is_admin, public_key, cipher, iv) ".
			"VALUE (".
				"'$name','$user','$pass', '$admin', ".
				"'$pub_key', '$cipher', '$iv')");
		if($result) $success = 'User added successfully!';
		else $error = 'Failed to add user!';
	}
}

ignore:
if($function == 'add'){
?>
<form id="fadd" method="POST" enctype="multipart/form-data">
	<input type="text" name="name" placeholder="Name">
	<input type="text" name="user" placeholder="Username">
	<div class="form-group">
		<input type="password" name="pass" placeholder="Password">
	</div>
	<div class="form-group">
		<input type="password" name="rpass" placeholder="Repeat Password">
	</div>
	<div class="form-group">
		<input type="checkbox" name="admin">
		<label for="admin">Administrator</label>
	</div>
	<input type="submit" value="Add User">
</form>
<script>
	$('#fadd input[type=password]').each((i, input)=>{
		$(input).change(e=>{
			if(input.value.length < 8){
				const elem = $(`<div class="validate error">Password must have more than 8 characters</div>`);
				$(input.parentElement).prepend(elem);
				setTimeout(()=>elem.remove(), 1500);
			}
		});
	});
	$('#fadd').submit(e=>{
		const [a, b] = $('#fadd input[type=password]');
		if(a.value.length < 8 || b.value.length < 8)
			e.preventDefault();
		else alert('Submitting...');
	});
</script>
<?php
}else{
?>
<h1>Users</h1>
<a href="?page=manage&type=user&function=add">
	<button>New</button>
</a>
<?php
}
?>
<?php
if(($success || $error) && !($success && $error)){
?>
	<div class="toast <?php echo $error ? 'error' : 'success'?>">
		<span><?php echo $error ? $error : $success ?>
	</div>
<?php
}
?>
