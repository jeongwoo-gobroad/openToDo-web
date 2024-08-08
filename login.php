<?php
require_once "dbaccess.php";

session_start();

function isVaildLogin($email, $pass, $db) {
    $query = $db->prepare("SELECT user_name, user_id FROM Users WHERE user_email = :em AND user_password = :pa");
    $query->execute(array(':em' => $email, ':pa' => $pass));
    $row   = $query->fetch(PDO::FETCH_ASSOC);

    if ($row != null) {
        $_SESSION['user_id'] = htmlentities($email);
        $_SESSION['user_name'] = htmlentities($row['user_name']);
        $_SESSION['user_key'] = $row['user_id'];

        return true;
    }

    return false;
}

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