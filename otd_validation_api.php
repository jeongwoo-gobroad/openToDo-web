<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php

// Function #1: Checking Login
function checkIfLoggedIn() {
    if (isset($_SESSION['user_id']) === false) {
        $_SESSION['failure'] = "Not logged in";
        header("Location: index.php");
        return;
    }

    return;
}

// Function #2: Checking duplicated email address
function doesEmailAlreadyExists($email, $db) {
    $stmt = $db->prepare("SELECT user_email FROM Users WHERE user_email = :em");
    $stmt->execute(array(':em' => $email));
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rows != null) {
        return true;
    }

    return false;
}

// Function #3: Checking login information
function isVaildLogin($email, $pass, $db) {
    $query = $db->prepare("SELECT user_name, user_id FROM Users WHERE user_email = :em AND user_password = :pa");
    $query->execute(array(':em' => $email, ':pa' => $pass));
    $row   = $query->fetch(PDO::FETCH_ASSOC);

    if ($row != null) {
        $_SESSION['user_id'] = htmlentities($email);
        $_SESSION['user_name'] = htmlentities($row['user_name']);
        $_SESSION['user_key'] = $row['user_id'];

        return true;
    }

    return false;
}

?>