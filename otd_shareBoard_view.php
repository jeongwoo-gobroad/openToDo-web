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
    $_SESSION['failure'] = "You have no permission to view this very board or board id doesn't exist";
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

?>
<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <?php echo "<title>" . $shareBoard_title . " ToDos::View Page</title>";?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <?php echo "<h1>" . $shareBoard_title . " ToDos</h1>"; ?>
            <?php echo "<h6>Board id: " . $shareBoard_id . "</h6>"; ?>
            <hr color = "#000000" noshade/>
            <p><a href = "index.php">Go to main page</a></p>
            <p><a href = "otd_shareBoard_add.php?board=<?= $shareBoard_id ?>">Add a new shareBoard ToDo</a></p>
            <p>
                <?php
                if ( isset($_SESSION['failure']) ) {
                    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
                    unset($_SESSION['failure']);
                }
                if ( isset($_SESSION['success']) ) {
                    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                    unset($_SESSION['success']);
                }
                // Personal ToDos
                echo("<h3>Personal ToDos</h3>");
                echo('<table border="2">'."\n");
                $stmt = $pdo->prepare("SELECT title, details, date_info, priority, Users.user_id, todo_id, user_name FROM shareBoard_todos JOIN Users
                                    ON shareBoard_todos.user_id = Users.user_id WHERE shareBoard_id = $shareBoard_id AND is_shared = 0 ORDER BY date_info");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row === false) {
                    echo('<h3>No rows found' . "</h3>\n");
                } else {
                    echo "<tr><td>";
                    echo(" By ");
                    echo("</td><td>");
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
                        echo(htmlentities($row['user_name']));
                        echo("</td><td>");
                        echo(htmlentities($row['date_info']));
                        echo("</td><td>");
                        echo(htmlentities($row['title']));
                        echo("</td><td>");
                        echo(htmlentities($row['details']));
                        echo("</td><td>");
                        echo(htmlentities($row['priority']));
                        echo("</td><td>");
                        if ($_SESSION['user_key'] == $row['user_id']) {
                            echo('<a href="otd_shareBoard_edit.php?todo_id=' .$row['todo_id']. '&board=' . $shareBoard_id . '">Edit</a> / ');
                            echo('<a href="otd_shareBoard_del.php?todo_id=' .$row['todo_id']. '&board=' . $shareBoard_id . '">Delete</a>');
                        }
                        echo("</td></tr>\n");
                    } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
                }
                ?>
                </table>
            </p>
            <p>
                <?php
                // Shared ToDos
                echo("<h3>Shared ToDos</h3>");
                echo('<table border="2">'."\n");
                $stmt = $pdo->prepare("SELECT title, details, date_info, priority, todo_id FROM shareBoard_todos WHERE is_shared = 1 AND shareBoard_id = $shareBoard_id ORDER BY date_info");
                $stmt->execute();
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
                        echo('<a href="otd_shareBoard_edit.php?todo_id=' .$row['todo_id'].  '&board=' . $shareBoard_id .  '">Edit</a> / ');
                        echo('<a href="otd_shareBoard_del.php?todo_id=' .$row['todo_id'].  '&board=' . $shareBoard_id .  '">Delete</a>');
                        echo("</td></tr>\n");
                    } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
                }
                ?>
                </table>
            </p>
            <?php
            if ($is_admin === true) {
                echo("<p>");
                echo("<ul><li>" . '<a href = "otd_shareBoard_adminPage.php?board=' . $shareBoard_id . '">Admin page' . "</a></li></ul>");
                echo("</p>");
            }
            ?>
        </div>
    </body>
</html>