<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>openToDo::WEB - Welcome Page</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
    <div class="container">
    <h1>Welcome to Project openToDo::WEB!</h1>
    <hr color = "#000000" noshade/>
    <h3>An open-source project of platform-independent ToDos management system.</h3>
    <?php
        if ( isset($_SESSION['success']) === true) {
            echo "<h3>Message From Server: " . htmlentities($_SESSION['success']) . "</h3>\n";
            unset($_SESSION['success']);
        } else if (isset($_SESSION['failure']) === true) {
            echo "<h3>Error message From Server: " . htmlentities($_SESSION['failure']) . "</h3>\n";
            unset($_SESSION['failure']);
        }
        if ( isset($_SESSION['user_name']) === true) {
            echo "<h3>Hello, " . htmlentities($_SESSION['user_name']) . "</h3>\n";
            echo '<h5><a href = "otd_view.php">Dive Into Your ToDos!</a></h5>';
            echo '<p><a href="logout.php">Logging out</a></p>';
        } else {
            echo '<p><a href="login.php">Please Log In</a></p>';
        }
    ?>
    <p>
    To register account, visit
    <a href="register.php">Register</a> / If you've already logged in, it should return to this very message.
    </p><p>
    If you've logged in, visit
    <a href="accountInfoView.php">Account Info Page</a> / Without logging in, it should fail with an error message.
    </p>
    </div>
</body>