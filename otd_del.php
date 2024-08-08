<?php
require_once "dbaccess.php";
session_start();

if (isset($_SESSION['user_id']) === false) {
    $_SESSION['failure'] = "Not logged in";
    header("Location: index.php");
    return;
}

if (isset($_POST['cancel'])) {
    header('Location: otd_view.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['todo_id'])) {
    $query = "DELETE FROM Todos WHERE todo_id = :tid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':tid' => $_POST['todo_id']));

    $_SESSION['success'] = "Record deleted";
    header('Location: otd_view.php');

    return;
} else {
    if (isset($_GET['todo_id']) === false) {
        $_SESSION['failure'] = "Missing todo id";
        header('Location: index.php');
        return;
    }

    $query = $pdo->prepare("SELECT * FROM Todos WHERE todo_id = :tid");
    $query->execute(array(":tid" => $_GET['todo_id']));
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        $_SESSION['failure'] = "Bad value for todo id";
        header('Location: index.php');
        return;
    } else if ($row['user_id'] != $_SESSION['user_key']) {
        $_SESSION['failure'] = "This user does not own this very todo";
        header("Location: index.php");
    }

    $usr_title = htmlentities($row['title']);
    $usr_details = htmlentities($row['details']);
    $usr_date = htmlentities($row['date_info']);
    $usr_pnum = htmlentities($row['priority']);
    $usr_todo_id = htmlentities($row['todo_id']);
}



?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <?php echo "<title>" . htmlentities($_SESSION['user_name']) . "'s ToDos::Deletion Page</title>";?>
    </head>
    <body>
        <div class="container">
            <?php
                echo "<h1>" . htmlentities($_SESSION['user_name']) . "'s ToDos::Deletion Page</h1>";
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
                </ul>
            </p>
            <form method="post">
                <input type = "hidden" name = "todo_id" value = "<?= $usr_todo_id ?>">
                <input type = "submit" name = "delete" value = "Delete">
                <input type = "submit" name = "cancel" value = "Cancel">
            </form>
        </div>
    </body>
</html>