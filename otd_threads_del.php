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
} else if (isset($_POST['delete'])) {
    $sql = "DELETE FROM Threads WHERE thread_id = :tid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            ':tid' => $_REQUEST['thread']
        )
    );

    $_SESSION['success'] = "Thread deleted";
    header("Location: otd_shareBoard_view.php?board=" . $_REQUEST['board']);
    return;
}

?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <title><?= $shareBoard_title ?> / <?= $thread_title ?> / Deletion Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <h1>Deleting <?= $shareBoard_title ?> / <?= $thread_title ?></h1>
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
            <p style="color:red">
                Are you sure to delete this very thread?
                All comments will be lost, and this cannot be undone.
            </p>
            <form method="POST">
                <input type="submit" name="delete" style="color:red" value="Delete">
                <input type="submit" name="cancel" value="Go back">
            </form>
        </div>
    </body>
</html>