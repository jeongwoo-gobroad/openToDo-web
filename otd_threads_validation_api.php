<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php

// Function #1: Checking if GETting thread id has succeeded
function checkGetThreadExists() {
    if (isset($_GET['thread']) === false) {
        $_SESSION['failure'] = "thread id doesn't exist";
        header("Location: index.php");
        return false;
    }

    return $_GET['thread'];
}

// Function #2: Checking permission to access to the selected thread
function checkThreadPermission($pdo, $shareBoard_id) {
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
        return false;
    }

    return true;
}

// Function #3: Getting thread title
function getThreadTitle($pdo, $thread_id) {
    $query = "SELECT thread_title FROM Threads_data WHERE thread_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid' => $thread_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['failure'] = "Internal error";
        header("Location: index.php");
        return;
    }

    $thread_title = htmlentities($row['thread_title']);

    return $thread_title;
}

// Function #4: Checking if the user is the admin of the given thread
function checkUserIsAdminOfTheThread($pdo, $thread_id) {
    $query = "SELECT user_id FROM Threads WHERE thread_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid' => $thread_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['user_id'] == $_SESSION['user_key']) {
        return true;
    } else {
        return false;
    }

    return true;
}

// Function #5: Getting thread details
function getThreadDetails($pdo, $thread_id) {
    $query = "SELECT thread_details FROM Threads_data WHERE thread_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid' => $thread_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['failure'] = "Internal error";
        header("Location: index.php");
        return;
    }

    $thread_details = htmlentities($row['thread_details']);

    return $thread_details;
}

// Function #5: Getting thread's recent update timestamp
function getThreadRecent($pdo, $thread_id) {
    $query = "SELECT thread_recent FROM Threads_data WHERE thread_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid' => $thread_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['failure'] = "Internal error";
        header("Location: index.php");
        return;
    }

    $thread_recent = htmlentities($row['thread_recent']);

    return $thread_recent;
}
?>