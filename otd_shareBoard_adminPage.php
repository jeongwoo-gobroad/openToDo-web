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
$is_admin = false;

// Basic check
if (isset($_GET['board']) === false) {
    $_SESSION['failure'] = "Board id doesn't exist";
    header("Location: index.php");
    return;
} else {
    $shareBoard_id = $_GET['board'];
}

// Validation
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
    } else {
        $_SESSION['failure'] = "You have no permission to view this very board or board id doesn't exist";
        header("Location: index.php");
        return;
    }
}

// Get title
$query = "SELECT shareBoard_title FROM shareBoard_info WHERE shareBoard_id = :sbid";
$stmt  = $pdo->prepare($query);
$stmt->execute(
    array(
        ':sbid' => $shareBoard_id
    )
);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$shareBoard_title = htmlentities($row['shareBoard_title']);

// core action
if (isset($_POST['save'])) {
    if (strlen(htmlentities($_POST['title'])) < 1) {
        $_SESSION['failure'] = "Title is too short";

        header('Location: otd_shareBoard_adminPage.php?board=' . $_POST['board_id']);
        return;
    } else if (strlen(htmlentities($_POST['title'])) > 120) {
        $_SESSION['failure'] = "Title is too long";

        header('Location: otd_shareBoard_adminPage.php?board=' . $_POST['board_id']);
        return;
    }

    $query = "UPDATE shareBoard_info SET shareBoard_title = :tl WHERE shareBoard_id = :sbid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tl' => htmlentities($_POST['title']),
            ':sbid' => $_POST['board_id']
        )
    );

    $_SESSION['success'] = "Altered title has been saved";
    header('Location: otd_shareBoard_adminPage.php?board=' . $_POST['board_id']);
    return;
} else if (isset($_POST['delete'])) {
    header('Location: otd_shareBoard_boardDeletion.php?board=' . $_POST['board_id']);
    return;
} else if (isset($_POST['cancel'])) {
    header('Location: otd_shareBoard_view.php?board=' . $_POST['board_id']);
    return;
}
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <?php echo "<title>" . $shareBoard_title . " ToDos::Admin Page</title>";?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php echo "<h1>" . $shareBoard_title . " ToDos Admin page</h1>"; ?>
            <?php echo "<h6>Board id: " . $shareBoard_id . "</h6>"; ?>
            <hr color = "#000000" noshade/>
            <?php
            if (isset($_SESSION['success'])) {
                echo ('<p style = "color: green;">' . htmlentities($_SESSION['success']) . "</p>\n");
                unset($_SESSION['success']);
            } else if (isset($_SESSION['failure'])) {
                echo ('<p style = "color: red;">' . htmlentities($_SESSION['failure']) . "</p>\n");
                unset($_SESSION['failure']);
            }
            ?>
            <p><a href = "index.php">Go to main page</a></p>
            <hr color = "#000000" noshade/>
            <p>Board title edit</p>
            <form method="post">
                <label for = "Title">Title: (up to 120 letters)</label>
                <input type = "textarea" name = "title" id = "Title" value = "<?= $shareBoard_title ?>"></br>

                <input type = "submit" name = "save" value = "Save">
                <input type = "hidden" name = "board_id" value = "<?= $shareBoard_id ?>">
            </form>

            <hr color = "#000000" noshade/>
            <p>Board deletion</p>
            <form method="post">
                <input type = "submit" name = "delete" value = "Delete">
                <input type = "hidden" name = "board_id" value = "<?= $shareBoard_id ?>">
            </form>

            <hr color = "#000000" noshade/>
            <form method="post">
                <input type = "submit" name = "cancel" value = "Go Back">
                <input type = "hidden" name = "board_id" value = "<?= $shareBoard_id ?>">
            </form>
        </div>
    </body>
</html>