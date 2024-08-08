<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
    session_start();
    if (isset($_SESSION['user_name']) === false) {
        $_SESSION['success'] = "Already Logged out";

    } else {
        session_destroy();
        session_start();
        $_SESSION['success'] = "Logged out";
    }
    header("Location: index.php");
    return;
?>