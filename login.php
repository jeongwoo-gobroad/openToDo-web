<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
require_once "otd_validation_api.php";

session_start();

if ( isset($_SESSION['user_id'] ) ) {
    header("Location: index.php");
    return;
}
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

$salt = 'justToBeSure';

if ( isset($_POST['email']) && isset($_POST['pass'])) {
    $email = htmlentities($_POST['email']);
    $pass = htmlentities($_POST['pass']);

    if ( strlen($email) < 1 || strlen($pass) < 1 ) {
        $_SESSION['failure'] = "Email and password are required";
        header("Location: login.php");
        return;
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['failure'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    } else {
        $password = hash('md5', $salt.$pass);
        
        if (isVaildLogin($email, $password, $pdo) === false) {
            $_SESSION['failure'] = "Wrong Email or Password";
            header("Location: login.php");
            return;
        }

        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>openToDo::WEB - Login Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
<h1>Please Log in</h1>
<hr color = "#000000" noshade/>
<?php
if ( isset($_SESSION['failure']) === true) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
    unset($_SESSION['failure']);
}
?>
<form method="POST">
    <label for="email">User Email: </label>
    <input type="text" name="email" id="email"><br/>
    <label for="pass_1">Password: </label>
    <input type="password" name="pass" id="pass_1"><br/>

    <input type="submit" value="Log in">
    <input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>