<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
session_start();

if (isset($_SESSION['user_id']) === false) {
    $_SESSION['failure'] = "Not logged in";
    header("Location: index.php");
    return;
}

if (isset($_POST['add'])) {
    if (strlen($_POST['date']) < 1 || strlen($_POST['title']) < 1 || strlen($_POST['details']) < 1 || strlen($_POST['priority']) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: otd_add.php");
        return;
    } else if (strlen($_POST['title']) > 120 || strlen($_POST['details']) > 500) {
        $_SESSION['failure'] = "Strings too long";
        header("Location: otd_add.php");
        return;
    } else {
        $sql = "INSERT INTO Todos (user_id, title, details, date_info, priority) VALUES (:id, :tl, :dl, :di, :pn)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':id' => htmlentities($_SESSION['user_key']),
            ':tl' => htmlentities($_POST['title']),
            ':dl' => htmlentities($_POST['details']),
            ':di' => htmlentities($_POST['date']),
            ':pn' => htmlentities($_POST['priority'])
        ));
        
        $_SESSION['success'] = "Record added";
        header("Location: otd_view.php");
    
        return;
    }
} else if (isset($_POST['cancel'])) {
    header("Location: otd_view.php");
    return;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <?php echo "<title>" . htmlentities($_SESSION['user_name']) . "'s ToDos::Add Page</title>"; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php
                echo "<h1>" . htmlentities($_SESSION['user_name']) . "'s ToDos::Add Page</h1>";
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
                <p></p>
                <input type = "submit" name = "add" value = "Add">
                <input type = "submit" name = "cancel" value = "Cancel">
            </form>
        </div>
    </body>
</html>