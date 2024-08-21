<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php

// Function #1: Checking if GETting board id has succeeded
function checkGetBoardDataExists() {
    if (isset($_GET['board']) === false) {
        $_SESSION['failure'] = "Board id doesn't exist";
        header("Location: ../index.php");
        return false;
    } 

    return $_GET['board'];
}

// Function #2: Checking if GETting todo id has succeeded
function checkGetTodoExists() {
    if (isset($_GET['todo_id']) === false) {
        $_SESSION['failure'] = "Todo id doesn't exist";
        header("Location: ../index.php");
        return false;
    }

    return $_GET['todo_id'];
}

// Function #3: Checking permission to access to the selected board
function checkBoardAccessPermission($pdo, $shareBoard_id) {
    $query = "SELECT user_role FROM shareBoard_users WHERE shareBoard_id = :sbid AND user_id = :usid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':sbid' => $shareBoard_id,
            ':usid' => $_SESSION['user_key']
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row;
}

// Function #4: Getting shareBoard title
function getShareboardTitle($pdo, $shareBoard_id) {
    $query = "SELECT shareBoard_title FROM shareBoard_info WHERE shareBoard_id = :sbid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':sbid' => $shareBoard_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['failure'] = "Internal error";
        header("Location: ../index.php");
        return;
    }

    $shareBoard_title = htmlentities($row['shareBoard_title']);

    return $shareBoard_title;
}


// Function #5: Checking permission to edit/delete shareBoard's todo data
function checkUserAbleToAlter($pdo, $todo_id) {
    $query = "SELECT is_shared, user_id FROM shareBoard_todos WHERE todo_id = :tid";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(
        array(
            ':tid' => $todo_id
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['is_shared'] == 0 && $row['user_id'] != $_SESSION['user_key']) {
        $_SESSION['failure'] = "You have no permission to delete this todo";
        header("Location: ../index.php");
        return false;
    }

    return true;
}

// Function #6: Checking if given shareBoard title already exists(but actually this is not important)
function doesTitleAlreadyExists($title, $pass, $db) {
    $stmt = $db->prepare("SELECT * FROM shareBoard_info WHERE shareBoard_title = :em AND shareBoard_pw = :pw");
    $stmt->execute(array(':em' => $title, ':pw' => $pass));
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rows != null) {
        return true;
    }

    return false;
}

// Function #7: Checking if the user is the admin of the given board
function checkUserIsAdmin($pdo, $shareBoard_id) {
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
        header("Location: ../index.php");
        return false;
    } else {
        if ($row['user_role'] == 1) {
            $is_admin = true;
        } else {
            $_SESSION['failure'] = "You have no permission to view this very board or board id doesn't exist";
            header("Location: ../index.php");
            return false;
        }
    }

    return true;
}
?>