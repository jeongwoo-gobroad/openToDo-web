<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
require_once "otd_validation_api.php";
require_once "otd_shareBoard_validation_api.php";
require_once "otd_threads_validation_api.php";

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
$comment_owner = getCommentOwner($pdo, $_REQUEST['comment']);

// Validation
if ($_SESSION['user_key'] != $comment_owner) {
    $_SESSION['failure'] = "It is NOT your comment";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
} else if ($_SESSION['user_key'] == $comment_owner) {
    $sql = "DELETE FROM Threads_comment WHERE comment_id = :cid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            ':cid' => $_REQUEST['comment']
        )
    );

    $sql = "UPDATE Threads_data SET thread_recent = :tr WHERE thread_id = :tid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':tr' => htmlentities(date("Y-m-d H:i:s")),
        ':tid' => $_REQUEST['thread']
    ));

    $_SESSION['success'] = "Comment deleted";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . "&thread=" . $_REQUEST['thread']);
    return;
}

?>