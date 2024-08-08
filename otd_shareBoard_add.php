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
$shareBoard_title = false;

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
    $_SESSION['failure'] = "You have no permission to edit this very board or board id doesn't exist";
    header("Location: index.php");
    return;
} else {
    if ($row['user_role'] == 1) {
        $is_admin = true;
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

// Actual Adding
if (isset($_POST['add'])) {
    if (strlen($_POST['date']) < 1 || strlen($_POST['title']) < 1 || strlen($_POST['details']) < 1 || strlen($_POST['priority']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_shareBoard_add.php?board=" . $shareBoard_id);
        return;
    } else if (strlen($_POST['title']) > 120 || strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_shareBoard_add.php?board=" . $shareBoard_id);
        return;
    } else {
        // Now inserting...
        if ($_POST['pors'] == "personal") {
            $sql = "INSERT INTO shareBoard_todos (shareBoard_id, user_id, title, details, date_info, priority, is_shared) VALUES (:shid, :id, :tl, :dl, :di, :pn, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':shid' => $shareBoard_id,
                ':id' => htmlentities($_SESSION['user_key']),
                ':tl' => htmlentities($_POST['title']),
                ':dl' => htmlentities($_POST['details']),
                ':di' => htmlentities($_POST['date']),
                ':pn' => htmlentities($_POST['priority'])
            ));
        } else {
            $sql = "INSERT INTO shareBoard_todos (shareBoard_id, user_id, title, details, date_info, priority, is_shared) VALUES (:shid, :id, :tl, :dl, :di, :pn, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':shid' => $shareBoard_id,
                ':id' => htmlentities($_SESSION['user_key']),
                ':tl' => htmlentities($_POST['title']),
                ':dl' => htmlentities($_POST['details']),
                ':di' => htmlentities($_POST['date']),
                ':pn' => htmlentities($_POST['priority'])
            ));
        }
        
        
        $_SESSION['success'] = "Record added";
        header("Location: otd_shareBoard_view.php?board=" . $shareBoard_id);
    
        return;
    }
} else if (isset($_POST['cancel'])) {
    header("Location: otd_shareBoard_view.php?board=" . $shareBoard_id);
    return;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <?php echo "<title>" . $shareBoard_title . " ToDos::Add Page</title>"; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php
                echo "<h1>" . $shareBoard_title . " ToDos::Add Page</h1>";
                if (isset($_SESSION['failure'])) {
                    echo ('<p style = "color: red;">' . htmlentities($_SESSION['failure']) . "</p>\n");
                    unset($_SESSION['failure']);
                }
            ?>
            <hr color = "#000000" noshade/>
            <form method="post">
                <label for = "Date">Date: </label>
                <input type = "date" name = "date" id = "Date"></br>
                <label for = "Title">Title: (up to 120 letters)</label>
                <input type = "textarea" name = "title" id = "Title"></br>
                <label for = "Details">Details: (up to 500 letters)</label>
                <input type = "textarea" name = "details" id = "Details" style="width:300px;height:200px;font-size:15px;"></br>
                <label for = "Priority">Priority: </label>
                <select name = "priority" id = "Priority">
                    <option value = "1">1(Least important)</option>
                    <option value = "2">2</option>
                    <option value = "3">3</option>
                    <option value = "4">4</option>
                    <option value = "5">5(Most important)</option>
                </select>
                <label for = "pors">Personal or Shared: </label>
                <select name = "pors" id = "pors">
                    <option value = "personal">Personal</option>
                    <option value = "shared">Shared</option>
                </select>
                <p></p>
                <input type = "submit" name = "add" value = "Add">
                <input type = "submit" name = "cancel" value = "Cancel">
            </form>
        </div>
    </body>
</html>