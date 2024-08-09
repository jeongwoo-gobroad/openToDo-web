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

if ( isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pass_a']) && isset($_POST['name']) ) {
    $email = htmlentities($_POST['email']);
    $pass = htmlentities($_POST['pass']);
    $pass_a = htmlentities($_POST['pass_a']);
    $name = htmlentities($_POST['name']);

    $num = preg_match('/[0-9]/u', $pass);
    $eng = preg_match('/[a-z]/u', $pass);
    $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u",$pass);

    if ( strlen($email) < 1 || strlen($pass) < 1 ) {
        $_SESSION['failure'] = "Email and password are required";
        header("Location: register.php");
        return;
    } else if (strlen($email) > 127 || strlen($pass) > 127) {
        $_SESSION['failure'] = "Email or password is too long";
        header("Location: register.php");
        return;
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['failure'] = "Email must have an at-sign (@)";
        header("Location: register.php");
        return;
    } else if (strlen($pass) < 10) {
        $_SESSION['failure'] = "Password should be at least 10 digits long";
        header("Location: register.php");
        return;
    } else if (preg_match("/\s/u", $pw) == true) {
        $_SESSION['failure'] = "Password should not include spaces";
        header("Location: register.php");
        return;
    } else if ($num == 0 || $eng == 0 || $spe == 0) {
        $_SESSION['failure'] = "Password should include digits, alphabets, special characters";
        header("Location: register.php");
        return;
    } else if ($pass !== $pass_a) {
        $_SESSION['failure'] = "Re-entered Password is not the same as the original one";
        header("Location: register.php");
        return;
    } else if (strlen($name) < 1) {
        $_SESSION['failure'] = "User Name required";
        header("Location: register.php");
        return;
    } else if (doesEmailAlreadyExists($email, $pdo) === true) {
        $_SESSION['failure'] = "Email Already registered";
        header("Location: register.php");
        return;
    } else {
        $password = hash('md5', $salt.$pass);
        $query = "INSERT INTO Users (user_email, user_name, user_password) VALUES (:ue, :un, :up)";
        $stmt  = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':ue' => $email,
                ':un' => $name,
                ':up' => $password
            )
        );
        
        $_SESSION['success'] = "Account Registered";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>openToDo::WEB - Register Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
<h1>Register Page</h1>
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
    <p>Password should be at least 10 digits long, including digits, alphabets, special characters</p>
    <label for="pass_1">Password: </label>
    <input type="password" name="pass" id="pass_1"><br/>
    <label for="pass_2">Re-enter Password: </label>
    <input type="password" name="pass_a" id="pass_2"><br/>
    <label for="name">User Name: </label>
    <input type="text" name="name" id="name"><br/>

    <input type="submit" value="Create">
    <input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>