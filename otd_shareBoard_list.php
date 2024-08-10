<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
require_once "dbaccess.php";
require_once "otd_validation_api.php";

session_start();

checkIfLoggedIn();
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>openToDo::WEB - shareBoard List Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
    <h1>shareBoard List of <?= $_SESSION['user_name'] ?></h1>
    <hr color = "#000000" noshade/>
    <?php
        $query = "SELECT shareBoard_title, shareBoard_info.shareBoard_id, user_role FROM shareBoard_users JOIN shareBoard_info ON 
                  shareBoard_users.shareBoard_id = shareBoard_info.shareBoard_id WHERE user_id = :usid";
        $stmt  = $pdo->prepare($query);
        $stmt->execute(
            array(
                ':usid' => $_SESSION['user_key']
            )
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row == false) {
            echo("<h3>No rows found</h3>");
        } else {
            echo("<ul>");
            do {
                $title = htmlentities($row['shareBoard_title']);
                $id    = $row['shareBoard_id'];
                $user_role = $row['user_role'];
                // 1 stands for admin
                if ($user_role == 1) {
                    echo('<a href = "' . "otd_shareBoard_view.php?board=$id" .'">' . "<li>Board title: " . $title . " / Board id: " . $id . " @admin</li></a>");
                } else {
                    echo('<a href = "' . "otd_shareBoard_view.php?board=$id" .'">' . "<li>Board title: " . $title . " / Board id: " . $id . "</li></a>");
                }
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            echo("</ul>");
        }
    ?>
    <p>
        <a href = "index.php">Go To Home</a>
    </p>
    </div>
</body>