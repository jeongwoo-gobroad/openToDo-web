<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "/volume1/web/openToDo_web/dbaccess.php";
require_once "/volume1/web/openToDo_web/otd_validation_api.php";
require_once "/volume1/web/openToDo_web/otd_shareBoard_validation_api.php";

session_start();

checkIfLoggedIn();

$shareBoard_id = false;
$is_admin = false;
$shareBoard_title = false;
$thread_id = false;

// Basic check
$shareBoard_id = checkGetBoardDataExists();
if ($shareBoard_id === false) {
    return;
}

// Validation
$row = checkBoardAccessPermission($pdo, $shareBoard_id);
if ($row == false) {
    $_SESSION['failure'] = "You have no permission to edit this very board or board id doesn't exist";
    header("Location: ../index.php");
    return;
} else {
    if ($row['user_role'] == 1) {
        $is_admin = true;
    }
}

// Get title
$shareBoard_title = getShareboardTitle($pdo, $shareBoard_id);

// Actual Adding
if (isset($_POST['start'])) {
    if (strlen($_POST['title']) < 1 || strlen($_POST['details']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_threads_create.php?board=" . $shareBoard_id);
        return;
    } else if (strlen($_POST['title']) > 120 || strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_threads_create.php?board=" . $shareBoard_id);
        return;
    } else {
        $sql = "INSERT INTO Threads (shareBoard_id, user_id) VALUES (:sbid, :id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':sbid' => $shareBoard_id,
            ':id' => htmlentities($_SESSION['user_key']),
        ));

        $sql = "SELECT MAX(thread_id) FROM Threads";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row == false) {
            $_SESSION['failure'] = "Critical error";
            header("Location: ../index.php");
            return;
        }

        $thread_id = $row['MAX(thread_id)'];

        $sql = "INSERT INTO Threads_data (thread_id, thread_title, thread_details, thread_recent) VALUES (:tid, :tl, :dl, :tim)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':tid' => $thread_id,
            ':tl' => htmlentities($_POST['title']),
            ':dl' => htmlentities($_POST['details']),
            ':tim' => htmlentities(date("Y-m-d H:i:s"))
        ));
        
        $_SESSION['success'] = "Thread started";
        header("Location: otd_threads_view.php?board=" . $shareBoard_id . "&thread=" . $thread_id);
    
        return;
    }
} else if (isset($_POST['cancel'])) {
    header("Location: ../shareBoard/otd_shareBoard_view.php?board=" . $shareBoard_id);
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "/volume1/web/openToDo_web/bootstrap.php"; ?>
    <title>openToDo::WEB - Thread Starting Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1><?= $shareBoard_title ?> / Thread Starting</h1>
        <hr color = "#000000" noshade/>
        <?php
        if ( isset($_SESSION['failure']) === true) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
            unset($_SESSION['failure']);
        }
        ?>
        <form method="POST">
            <label for="title">Thread Title: (up to 120 letters)</label>
            <!--<input type="text" name="title" value="" style="width:600px;height:50px;font-size:15px;" id="title"><br/>-->
            <p>
                <textarea style="resize: none;width:600px;height:50px;" name = "title" id = "title"></textarea>
            </p>
            <label for = "Details">Details: (up to 500 letters)</label>
            <!--<input type = "textarea" name = "details" value="" id = "Details" cols = "30" rows="10" style="width:600px;height:200px;font-size:15px;"></br>-->
            <p>
                <textarea style="resize: none;width:600px;height:200px;" name = "details" id = "Details"></textarea>
            </p>
            <input type="submit" name="start" value="Start">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>