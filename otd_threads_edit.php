<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
require_once "otd_validation_api.php";
require_once "otd_shareBoard_validation_api.php";
require_once "otd_threads_validation_api.php";

session_start();

checkIfLoggedIn();

$shareBoard_id = false;
$is_admin_of_the_thread = false;
$shareBoard_title = false;
$thread_id = false;
$thread_title = false;
$thread_details = false;
$thread_last_update = false;
$is_thread_owner = false;

// Basic check
$shareBoard_id = checkGetBoardDataExists();
if ($shareBoard_id === false) {
    return;
}
$thread_id = checkGetThreadExists();
if ($thread_id === false) {
    return;
}

// Validation
if (checkThreadPermission($pdo, $shareBoard_id) === false) {
    $_SESSION['failure'] = "You have no permission to see this thread";
    header("Location: index.php");
    return;
}

// Get some datas
$shareBoard_title   = getShareboardTitle($pdo, $shareBoard_id);
$thread_title       = getThreadTitle($pdo, $thread_id);
$thread_details     = getThreadDetails($pdo, $thread_id);
$thread_last_update = getThreadRecent($pdo, $thread_id);
$thread_owner       = getThreadOwner($pdo, $thread_id);

if ($thread_owner == $_SESSION['user_key']) {
    $is_thread_owner = true;
} else {
    $_SESSION['failure'] = "You have no permission to modify this thread";
    header("Location: index.php");
    return;
}

if (isset($_POST['cancel'])) {
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
} else if (isset($_POST['edit'])) {
    if (strlen($_POST['title']) < 1 || strlen($_POST['details']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_threads_edit.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
        return;
    } else if (strlen($_POST['title']) > 120 || strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_threads_edit.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
        return;
    }

    $titleString = htmlentities($_POST['title']);
    $detailString = htmlentities($_POST['details']);

    $sql = "UPDATE Threads_data SET thread_title = :tt, thread_details = :td, thread_recent = :tr WHERE thread_id = :tid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':tt' => $titleString,
        ':td' => $detailString,
        ':tr' => htmlentities(date("Y-m-d H:i:s")),
        ':tid' => $_REQUEST['thread']
    ));

    $_SESSION['success'] = "Thread edited";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
}

?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <title><?= $shareBoard_title ?> / <?= $thread_title ?> / Editing Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <h1>Editing <?= $shareBoard_title ?> / <?= $thread_title ?></h1>
            <hr color = "#000000" noshade/>
            <?php
                if ( isset($_SESSION['failure']) ) {
                    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
                    unset($_SESSION['failure']);
                }
                if ( isset($_SESSION['success']) ) {
                    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                    unset($_SESSION['success']);
                }
            ?>
            <p>
                <?= $thread_details ?>
            </p>
            <h6>Thread id: <?= $thread_id ?></h6>
            <h6>Last update: <?= $thread_last_update ?></h6>
            <hr color = "#000000" noshade/>
            <form method="POST">
                <label for="title">Thread Title: (up to 120 letters)</label>
                <!--<input type="text" name="title" value="" style="width:600px;height:50px;font-size:15px;" id="title"><br/>-->
                <p>
                    <textarea style="resize: none;width:600px;height:50px;" name = "title" id = "title"><?= $thread_title ?></textarea>
                </p>
                <label for = "Details">Details: (up to 500 letters)</label>
                <!--<input type = "textarea" name = "details" value="" id = "Details" cols = "30" rows="10" style="width:600px;height:200px;font-size:15px;"></br>-->
                <p>
                    <textarea style="resize: none;width:600px;height:200px;" name = "details" id = "Details"><?= $thread_details ?></textarea>
                </p>
                <input type="submit" name="edit" style="color:blue" value="Edit">
                <input type="submit" name="cancel" value="Go back">
            </form>
        </div>
    </body>
</html>