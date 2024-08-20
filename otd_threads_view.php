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

if (isset($_POST['comment'])) {
    if (strlen($_POST['details']) < 1) {
        $_SESSION['failure'] = "Comment too short";
        header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . '&thread=' . $_REQUEST['thread']);
        return;
    } else if (strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Comment too long";
        header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . '&thread=' . $_REQUEST['thread']);
        return;
    }

    $timeStamp = date("Y-m-d H:i:s");

    $query = "INSERT INTO Threads_comment (thread_id, user_id, comment_date, comment_details) VALUES (:tid, :id, :cda, :cdet)";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid'  => $_REQUEST['thread'],
            ':id'   => $_SESSION['user_key'],
            ':cda'  => htmlentities($timeStamp),
            ':cdet' => htmlentities($_POST['details'])
        )
    );

    $query = "UPDATE Threads_data SET thread_recent = :upd WHERE thread_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid'  => $_REQUEST['thread'],
            ':upd'  => htmlentities($timeStamp)
        )
    );

    $_SESSION['success'] = "Commented";
    header("Location: otd_threads_view.php?board=" . $_REQUEST['board'] . '&thread=' . $_REQUEST['thread']);
    return;
}

?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <title><?= $shareBoard_title ?> / <?= $thread_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <h1><?= $shareBoard_title ?> / <?= $thread_title ?></h1>
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
            <p><a href = "index.php">Go to main page</a></p>
            <p><a href = "otd_shareBoard_view.php?board=<?= $shareBoard_id ?>">Go back to shareBoard Page</a></p>
            <hr color = "#000000" noshade/>
            <h3>Comments</h3>
            <form method="post">
                <label for = "Reply">Comment: (up to 500 letters)</label>
                <input type = "textarea" name = "details" id = "Reply" style="width:300px;height:200px;font-size:15px;"></br>
                <input type = "submit" name = "comment" value = "Comment">
            </form>
            <?php
            $stmt = $pdo->prepare("SELECT user_name, Users.user_id, comment_id, comment_date, comment_details FROM 
                                   Threads_comment JOIN Users ON Users.user_id = Threads_comment.user_id WHERE thread_id = :tid");
            $stmt->execute(
                array(
                    'tid' => $thread_id
                )
            );
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                echo('<h3>No comments found' . "</h3>\n");
            } else {
                echo('<ul>');
                do {
                    if ($row['user_id'] == $_SESSION['user_key']) {
                        echo('<li>');
                        echo('Comment: ' . $row['comment_details'] . ' When: ' . $row['comment_date']);
                        echo('<a href = otd_threads_comment_del?board=' . $shareBoard_id . '&thread=' . $thread_id . '&comment=' . $row['comment_id'] . '"> Delete</a>');
                        echo('<a href = otd_threads_comment_edit?board=' . $shareBoard_id . '&thread=' . $thread_id . '&comment=' . $row['comment_id'] . '"> Edit</a>');
                        echo('</li>');
                    } else {
                        echo('<li>');
                        echo('Comment: ' . $row['comment_details'] . ' When: ' . $row['comment_date']);
                        echo('</li>');
                    }
                } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
                echo('</ul>');
            }
            ?>
        </div>
    </body>
</html>