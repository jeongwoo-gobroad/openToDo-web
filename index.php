<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>openToDo::Web - Welcome Page</title>
<?php require_once "bootstrap.php"; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
    <h1>Welcome to Project openToDo::Web!</h1>
    <h5>(c) 2024 Jeongwoo Kim, KNU CSE</h5>
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
            echo "<ul>";
            echo '<li><h5><a href = "myTodo/otd_view.php">Dive Into Your ToDos!</a></h5></li>';
            echo '<li><h5><a href = "shareBoard/otd_shareBoard_create.php">Create a ShareBoard</a></h5></li>';
            echo '<li><h5><a href = "shareBoard/otd_shareBoard_join.php">Join a ShareBoard</a></h5></li>';
            echo '<li><h5><a href = "shareBoard/otd_shareBoard_list.php">Participated ShareBoards lists</a></h5></li>';
            echo '<li><p><a href="logout.php">Log out</a></p></li>';
            echo "</ul>";
        } else {
            echo '<p><a href="login.php">Please Log In</a></p>';
        }
    ?>
    <p>
    To register account, visit
    <a href="register.php">Register</a> / If you've already logged in, it should return to this very page.
    </p><p>
    If you've logged in, visit
    <a href="accountInfoView.php">Account Info Page</a> / Without logging in, it should fail with an error message.
    </p><p>
    To take a look at the source code,
    <a href="https://github.com/jeongwoo-gobroad/openToDo-web">openToDo GitHub Page</a> / if you are interested.
    </p>
    </div>
</body>