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

if ( isset($_POST['title']) && isset($_POST['pass']) && isset($_POST['pass_a'])) {
    $title = htmlentities($_POST['title']);
    $pass = htmlentities($_POST['pass']);
    $pass_a = htmlentities($_POST['pass_a']);

    $num = preg_match('/[0-9]/u', $pass);
    $eng = preg_match('/[a-z]/u', $pass);
    $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u",$pass);

    if ( strlen($title) < 1 || strlen($pass) < 1 ) {
        $_SESSION['failure'] = "shareBoard title and password are required";
        header("Location: otd_shareBoard_create.php");
        return;
    } else if (strlen($email) > 120 || strlen($pass) > 127) {
        $_SESSION['failure'] = "shareBoard title or password is too long";
        header("Location: otd_shareBoard_create.php");
        return;
    } else if (strlen($pass) < 10) {
        $_SESSION['failure'] = "Password should be at least 10 digits long";
        header("Location: otd_shareBoard_create.php");
        return;
    } else if (preg_match("/\s/u", $pw) == true) {
        $_SESSION['failure'] = "Password should not include spaces";
        header("Location: otd_shareBoard_create.php");
        return;
    } else if ($num == 0 || $eng == 0 || $spe == 0) {
        $_SESSION['failure'] = "Password should include digits, alphabets, special characters";
        header("Location: otd_shareBoard_create.php");
        return;
    } else if ($pass !== $pass_a) {
        $_SESSION['failure'] = "Re-entered Password is not the same as the original one";
        header("Location: otd_shareBoard_create.php");
        return;
    } else {
        $password = hash('md5', $salt.$pass);

        if (doesTitleAlreadyExists($title, $password, $pdo)) {
            $_SESSION['failure'] = "Please try another title or password";
            header("Location: otd_shareBoard_create.php");
            return;
        }

        $query = "INSERT INTO shareBoard_info (shareBoard_pw, shareBoard_title) VALUES (:pw, :tt)";
        $stmt  = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':pw' => $password,
                ':tt' => $title
            )
        );
        $query = "SELECT shareBoard_id FROM shareBoard_info WHERE shareBoard_title = :tt AND shareBoard_pw = :pw";
        $stmt  = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':pw' => $password,
                ':tt' => $title
            )
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $shareBoard_id = $row['shareBoard_id'];
        $query = "INSERT INTO shareBoard_users (shareBoard_id, user_id, user_role) VALUES (:sbid, :usid, :usrl)";
        $stmt  = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':sbid' => $shareBoard_id,
                ':usid' => $_SESSION['user_key'],
                ':usrl' => 1
            )
        );
        
        $_SESSION['success'] = "Board Created";
        header("Location: otd_shareBoard_view.php?board=" . $shareBoard_id);
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "/volume1/web/openToDo_web/bootstrap.php"; ?>
<title>openToDo::WEB - ShareBoard Creating Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
<h1>Create a ShareBoard</h1>
<hr color = "#000000" noshade/>
<?php
if ( isset($_SESSION['failure']) === true) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
    unset($_SESSION['failure']);
}
?>
<form method="POST">
    <label for="title">ShareBoard Title: (up to 120 letters)</label>
    <p>
        <textarea style="resize: none;width:600px;height:50px;" name = "title" id = "title"></textarea>
    </p>
    <p>Password should be at least 10 digits long, including digits, alphabets, special characters</p>
    <label for="pass_1">Password: </label>
    <input type="password" name="pass" id="pass_1"><br/>
    <label for="pass_2">Re-enter Password: </label>
    <input type="password" name="pass_a" id="pass_2"><br/>

    <input type="submit" value="Create">
    <input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>