<?php   
    include_once 'utils.php';
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        include_once 'db_connect.php';
        foreach(array('user', 'pass') as $key)
            if(!isset($_POST[$key])) header('Location: login.php');
        $user = $conn->real_escape_string($_POST['user']);
        $pass = hash('sha256', $conn->real_escape_string($_POST['pass']));
        $login = $conn->query("SELECT * FROM voters WHERE username='$user' AND password='$pass'");
        if(!$login || $login->num_rows != 1) header('Location: login.php?err=invalid_creds');
        $login = $login->fetch_assoc();
        session_start();
        foreach($login as $key=>$val){
            if($key == 'password') continue;
            $_SESSION[$key] = $val;
        }
        header('Location: home.php');
    }
?>
<style>
    * { margin: 0; }
    body { 
        position: absolute;
        top: 0; left: 0; bottom: 0; right: 0;
        display: grid;
        justify-content: center;
        align-content: center;
        min-width: 100vw;
        min-height: 100vh;
    }
    form {
        padding: 80px;
        display: grid;
        grid-auto-flow: row;
        grid-gap: 15px;
        align-items: center;
        width: 400px;
        height: 160px;
        border: 1px solid black;
    }
    form input {
        text-align: center;
        justify-self: center;
        width: 240px;
        height: 40px;
        border: 1px solid gray;
        line-height: 1.4rem;
        border-radius: 10px;
        font-weight: bolder;
        -moz-border-box: 1px 2px black;
    }
    form input:hover {
        background-color: #bfbfbf;
    }
</style>
<body>
    <form action="" method="POST">
        <input type="text" name="user" placeholder="Username">
        <input type="password" name="pass" placeholder="Password">
        <input type="submit" name="submit" value="Login">
    </form>
</body>