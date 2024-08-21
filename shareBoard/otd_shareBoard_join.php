<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "/volume1/web/openToDo_web/dbaccess.php";
require_once "/volume1/web/openToDo_web/otd_validation_api.php";
require_once "/volume1/web/openToDo_web/otd_shareBoard_validation_api.php";

session_start();

checkIfLoggedIn();

if (isset($_POST['cancel'])) {
    header("Location: ../index.php");
    return;
}

$salt = 'justToBeSure';

if ( isset($_POST['sbid']) && isset($_POST['pass'])) {
    $sbid = htmlentities($_POST['sbid']);
    $pass = htmlentities($_POST['pass']);

    if ( strlen($sbid) < 1 || strlen($pass) < 1 ) {
        $_SESSION['failure'] = "shareBoard ID and password are required";
        header("Location: otd_shareBoard_join.php");
        return;
    } else {
        $password = hash('md5', $salt.$pass);
        
        $query = $pdo->prepare("SELECT shareBoard_id FROM shareBoard_info WHERE shareBoard_id = :sbid AND shareBoard_pw = :pw");
        $query->execute(array(':sbid' => $sbid, ':pw' => $password));
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($row == false) {
            $_SESSION['failure'] = "Wrong shareBoard ID or Password";
            header("Location: otd_shareBoard_join.php");
            return;
        } else {
            $sbid = $row['shareBoard_id'];

            $query = $pdo->prepare("INSERT INTO shareBoard_users (shareBoard_id, user_id, user_role) VALUES (:sbid, :uuid, :usrl)");
            $query->execute(array(':sbid' => $sbid, ':uuid' => $_SESSION['user_key'], ':usrl' => 0));

            header("Location: otd_shareBoard_view.php?board=" . $sbid);
            return;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "/volume1/web/openToDo_web/bootstrap.php"; ?>
<title>openToDo::WEB - shareBoard Join Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
<h1>shareBoard Join</h1>
<hr color = "#000000" noshade/>
<?php
if ( isset($_SESSION['failure']) === true) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
    unset($_SESSION['failure']);
}
?>
<form method="POST">
    <label for="sbid">shareBoard ID: </label>
    <input type="text" name="sbid" id="sbid"><br/>
    <label for="pass_1">Password: </label>
    <input type="password" name="pass" id="pass_1"><br/>

    <input type="submit" value="Join">
    <input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>