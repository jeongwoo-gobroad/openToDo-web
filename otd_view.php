<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
session_start();

if (isset($_SESSION['user_id']) === false) {
    $_SESSION['failure'] = "Not logged in";
    header("Location: index.php");
    return;
}

if (isset($_POST['sort'])) {
    header("Location: otd_view.php?orderType=" . htmlentities($_POST['orderType']));
}

$orderType = false;
if (isset($_GET['orderType']) === false) {
    $orderType = "date";
} else if ($_GET['orderType'] == "date") {
    $orderType = htmlentities($_GET['orderType']);
} else if ($_GET['orderType'] == "priority") {
    $orderType = htmlentities($_GET['orderType']);
} else {
    $orderType = "date";
}
?>
<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <?php echo "<title>" . htmlentities($_SESSION['user_name']) . "'s ToDos::View Page</title>";?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php echo "<h1>" . htmlentities($_SESSION['user_name']) . "'s ToDos</h1>"; ?>
            <hr color = "#000000" noshade/>
            <p><a href = "otd_add.php">Add a new ToDo</a></p>
            <p><a href = "index.php">Go to main page</a></p>
            <p>
                <form method="post">
                    <label for = "sort">Sort by: </label>
                    <select name = "orderType" id = "sort">
                        <option value = "" selected><?= $orderType ?></option>
                        <option value = "date">date</option>
                        <option value = "priority">priority</option>
                    </select>
                    <input type = "submit" name = "sort" value = "Confirm">
                </form>
            </p>
            <?php
            if ( isset($_SESSION['failure']) ) {
                echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
                unset($_SESSION['failure']);
            }
            if ( isset($_SESSION['success']) ) {
                echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                unset($_SESSION['success']);
            }
            echo('<table border="2">'."\n");
            if ($orderType == "date") {
                $stmt = $pdo->prepare("SELECT title, details, date_info, priority, todo_id FROM Todos WHERE user_id = :id ORDER BY date_info");
            } else if ($orderType == "priority") {
                $stmt = $pdo->prepare("SELECT title, details, date_info, priority, todo_id FROM Todos WHERE user_id = :id ORDER BY priority DESC");
            } else {
                $_SESSION['failure'] = "Internal Error";
                header("Location: index.php");
            }
            $stmt->execute(array(':id' => $_SESSION['user_key']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                echo('<h3>No rows found' . "</h3>\n");
            } else {
                echo "<tr><td>";
                echo(" Date ");
                echo("</td><td>");
                echo(" Title ");
                echo("</td><td>");
                echo(" Details ");
                echo("</td><td>");
                echo(" Priority ");
                echo("</td><td>");
                echo(' Modify ');
                echo("</td></tr>\n");
                do {
                    echo "<tr><td>";
                    echo(htmlentities($row['date_info']));
                    echo("</td><td>");
                    echo(htmlentities($row['title']));
                    echo("</td><td>");
                    echo(htmlentities($row['details']));
                    echo("</td><td>");
                    echo(htmlentities($row['priority']));
                    echo("</td><td>");
                    echo('<a href="otd_edit.php?todo_id='.$row['todo_id'].'">Edit</a> / ');
                    echo('<a href="otd_del.php?todo_id='.$row['todo_id'].'">Delete</a>');
                    echo("</td></tr>\n");
                } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            }
            ?>
            </table>
        </div>
    </body>
</html>