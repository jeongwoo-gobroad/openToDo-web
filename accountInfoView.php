<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
require_once "otd_validation_api.php";

session_start();

checkIfLoggedIn();
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>openToDo::WEB - Account Info Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
<h1>Account Infos</h1>
<hr color = "#000000" noshade/>
<ul>
    <?php
        require_once "dbaccess.php";

        $email = $_SESSION['user_id'];
        $query = $pdo->prepare("SELECT user_name, user_email, user_id FROM Users WHERE user_email = :em");
        $query->execute(array(':em' => $email));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        echo("<li>" . 'Name: ' . htmlentities($row['user_name']) . "</li>\n");
        echo("<li>" . 'Email: ' . htmlentities($row['user_email']) . "</li>\n");
        echo("<li>" . 'DB ID: ' . htmlentities($row['user_id']) . "</li>\n");
    ?>
<ul>
<p>
    <a href = "index.php">Go To Home</a>
</p>
</div>
</body>