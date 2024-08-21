<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "/volume1/web/openToDo_web/dbaccess.php";
require_once "/volume1/web/openToDo_web/otd_validation_api.php";
require_once "/volume1/web/openToDo_web/otd_shareBoard_validation_api.php";
require_once "/volume1/web/openToDo_web/otd_threads_validation_api.php";

session_start();

checkIfLoggedIn();

$shareBoard_id = false;
$thread_id = false;
$is_comment_owner = false;

// Basic check
$shareBoard_id = checkGetBoardDataExists();
if ($shareBoard_id === false) {
    return;
}
$thread_id = checkGetThreadExists();
if ($thread_id === false) {
    return;
}
$thread_title   = getThreadTitle($pdo, $thread_id);
$thread_details = getThreadDetails($pdo, $thread_id);
$comment_owner  = getCommentOwner($pdo, $_REQUEST['comment']);
$comment_details = getCommentDetails($pdo, $_REQUEST['comment']);

// Validation
if ($_SESSION['user_key'] != $comment_owner) {
    $_SESSION['failure'] = "It is NOT your comment";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
}

if (isset($_POST['cancel'])) {
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
} else if (isset($_POST['edit'])) {
    if (strlen($_POST['details']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_threads_edit.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
        return;
    } else if (strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_threads_edit.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
        return;
    }

    $detailString = htmlentities($_POST['details']);

    $sql = "UPDATE Threads_comment SET comment_details = :cdet, comment_date = :cdat WHERE comment_id = :cid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':cdet' => $detailString,
        ':cdat' => htmlentities(date("Y-m-d H:i:s")),
        ':cid' => $_REQUEST['comment']
    ));

    $sql = "UPDATE Threads_data SET thread_recent = :tr WHERE thread_id = :tid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':tr' => htmlentities(date("Y-m-d H:i:s")),
        ':tid' => $_REQUEST['thread']
    ));

    $_SESSION['success'] = "comment edited";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
}

?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "/volume1/web/openToDo_web/bootstrap.php"; ?>
    <title><?= $shareBoard_title ?> / <?= $thread_title ?> / Comment editing page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <h1>Editing comment from <?= $shareBoard_title ?> / <?= $thread_title ?></h1>
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
            <hr color = "#000000" noshade/>
            <form method="POST">
                <label for = "Details">Comment: (up to 500 letters)</label>
                <p>
                    <textarea style="resize: none;width:600px;height:50px;" name = "details" id = "Details"><?= $comment_details ?></textarea>
                </p>
                <input type="submit" name="edit" style="color:blue" value="Edit">
                <input type="submit" name="cancel" value="Go back">
            </form>
        </div>
    </body>
</html>