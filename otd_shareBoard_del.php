<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
session_start();

if (isset($_SESSION['user_id']) === false) {
    $_SESSION['failure'] = "Not logged in";
    header("Location: index.php");
    return;
}

$shareBoard_id = false;
$todo_id = false;
$shareBoard_title = false;
$is_admin = false;
$is_personal = false;

// Basic check
if (isset($_GET['board']) === false || isset($_GET['todo_id']) === false) {
    $_SESSION['failure'] = "Board id or Todo id doesn't exist";
    header("Location: index.php");
    return;
} else {
    $shareBoard_id = $_GET['board'];
    $todo_id = $_GET['todo_id'];
}

// Validation #1: Able to see this board?
$query = "SELECT user_role FROM shareBoard_users WHERE shareBoard_id = :sbid AND user_id = :usid";
$stmt  = $pdo->prepare($query);
$stmt->execute(
    array(
        ':sbid' => $shareBoard_id,
        ':usid' => $_SESSION['user_key']
    )
);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row == false) {
    $_SESSION['failure'] = "You have no permission to view this very board or board id doesn't exist";
    header("Location: index.php");
    return;
} else {
    if ($row['user_role'] == 1) {
        $is_admin = true;
    }
}

// Validation #2: Able to edit this record?
$query = "SELECT is_shared, user_id FROM shareBoard_todos WHERE todo_id = :tid";
$stmt  = $pdo->prepare($query);
$stmt->execute(
    array(
        ':tid' => $todo_id
    )
);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['is_shared'] == 0 && $row['user_id'] != $_SESSION['user_key']) {
    $_SESSION['failure'] = "You have no permission to delete this todo";
    header("Location: index.php");
    return;
}

// now start...
$query = "SELECT shareBoard_title FROM shareBoard_info WHERE shareBoard_id = :sbid";
$stmt  = $pdo->prepare($query);
$stmt->execute(
    array(
        ':sbid' => $shareBoard_id
    )
);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row == false) {
    $_SESSION['failure'] = "Internal error";
    header("Location: index.php");
    return;
}
$shareBoard_title = htmlentities($row['shareBoard_title']);

// Now really starting...
if (isset($_POST['cancel'])) {
    header('Location: otd_shareBoard_view.php?board=' . $_POST['board_id']);
    return;
}

if (isset($_POST['delete']) && isset($_POST['todo_id'])) {
    $query = "DELETE FROM shareBoard_todos WHERE todo_id = :tid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':tid' => $_POST['todo_id']));

    $_SESSION['success'] = "Record deleted";
    header('Location: otd_shareBoard_view.php?board=' . $_POST['board_id']);

    return;
} else {
    $query = $pdo->prepare("SELECT * FROM shareBoard_todos WHERE todo_id = :tid");
    $query->execute(array(":tid" => $todo_id));
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['failure'] = "Bad value for todo id";
        header('Location: index.php');
        return;
    }

    $usr_title = htmlentities($row['title']);
    $usr_details = htmlentities($row['details']);
    $usr_date = htmlentities($row['date_info']);
    $usr_pnum = htmlentities($row['priority']);
    $usr_todo_id = htmlentities($row['todo_id']);
    if ($row['is_shared'] == 1) {
        $usr_shared = "Shared";
    } else {
        $usr_shared = "Personal";
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <?php echo "<title>" . $shareBoard_title . " ToDos::Deletion Page</title>";?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php
                echo "<h1>" . $shareBoard_title . " ToDos::Deletion Page</h1>";
                if (isset($_SESSION['failure'])) {
                    echo ('<p style = "color: red;">' . htmlentities($_SESSION['failure']) . "</p>\n");
                    unset($_SESSION['failure']);
                }
            ?>
            <p>Confirm: Deleting</p>
            <hr color = "#000000" noshade/>
            <p>
                <ul>
                    <li> Title: <?= $usr_title ?> </li>
                    <li> Date: <?= $usr_date ?> </li>
                    <li> Details: <?= $usr_details ?> </li>
                    <li> Priority: <?= $usr_pnum ?> </li>
                    <li> Shared: <?= $usr_shared ?> </li>
                </ul>
            </p>
            <form method="post">
                <input type = "hidden" name = "todo_id" value = "<?= $usr_todo_id ?>">
                <input type = "hidden" name = "board_id" value = "<?= $shareBoard_id ?>">
                <input type = "submit" name = "delete" value = "Delete">
                <input type = "submit" name = "cancel" value = "Cancel">
            </form>
        </div>
    </body>
</html>