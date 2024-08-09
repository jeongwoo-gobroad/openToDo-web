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

// now really starting...
$query = "SELECT * FROM shareBoard_todos WHERE todo_id = :tid";
$stmt  = $pdo->prepare($query);
$stmt->execute(
    array(
        ':tid' => $todo_id
    )
);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row == false) {
    $_SESSION['failure'] = "Internal error";
    header("Location: index.php");
    return;
}
$usr_title = htmlentities($row['title']);
$usr_details = htmlentities($row['details']);
$usr_date = htmlentities($row['date_info']);
$usr_pnum = htmlentities($row['priority']);
$usr_todo_id = htmlentities($row['todo_id']);

if (isset($_POST['save'])) {
    if (strlen($_POST['date']) < 1 || strlen($_POST['title']) < 1 || strlen($_POST['details']) < 1 || strlen($_POST['priority']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_shareBoard_edit.php?todo_id=" . $_POST['todo_id'] . "&board=" . $_POST['board_id']);
        return;
    } else if (strlen($_POST['title']) > 120 || strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_shareBoard_edit.php?todo_id=" . $_POST['todo_id'] . "&board=" . $_POST['board_id']);
        return;
    } else {
        $sql = "UPDATE shareBoard_todos SET title = :tl, details = :dl, date_info = :di, priority = :pn WHERE todo_id = :tid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':tid' => htmlentities($_POST['todo_id']),
            ':tl' => htmlentities($_POST['title']),
            ':dl' => htmlentities($_POST['details']),
            ':di' => htmlentities($_POST['date']),
            ':pn' => htmlentities($_POST['priority'])
        ));
        
        $_SESSION['success'] = "Record Edited";
        header("Location: otd_shareBoard_view.php?board=" . $_POST['board_id']);
    
        return;
    }
} else if (isset($_POST['cancel'])) {
    header("Location: otd_shareBoard_view.php?board=" . $_POST['board_id']);
    return;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <?php echo "<title>" . $shareBoard_title . " ToDos::Edit Page</title>";?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php
                echo "<h1>" . $shareBoard_title . "'s ToDos::Edit Page</h1>";
                if (isset($_SESSION['failure'])) {
                    echo ('<p style = "color: red;">' . htmlentities($_SESSION['failure']) . "</p>\n");
                    unset($_SESSION['failure']);
                }
            ?>
            <hr color = "#000000" noshade/>
            <form method="post">
                <label for = "Date">Date: </label>
                <input type = "date" name = "date" id = "Date" value = "<?= $usr_date ?>"></br>
                <label for = "Title">Title: (up to 120 letters)</label>
                <input type = "textarea" name = "title" id = "Title" value = "<?= $usr_title ?>"></br>
                <label for = "Details">Details: (up to 500 letters)</label>
                <input type = "textarea" name = "details" id = "Details" value = "<?= $usr_details ?>" style="width:300px;height:200px;font-size:15px;"></br>
                <label for = "Priority">Priority: </label>
                <select name = "priority" id = "Priority">
                    <option value = "" selected><?= $usr_pnum ?></option>
                    <option value = "1">1(Least important)</option>
                    <option value = "2">2</option>
                    <option value = "3">3</option>
                    <option value = "4">4</option>
                    <option value = "5">5(Most important)</option>
                </select>
                <p></p>
                <input type = "submit" name = "save" value = "Save">
                <input type = "submit" name = "cancel" value = "Cancel">
                <input type = "hidden" name = "todo_id" value = "<?= $usr_todo_id ?>">
                <input type = "hidden" name = "board_id" value = "<?= $shareBoard_id ?>">
            </form>
        </div>
    </body>
</html>