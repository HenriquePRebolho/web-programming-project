<?php
    session_start();

    $userId = $_SESSION["user_id"];
    $lastOnline = $_POST["lastOnline"];
    echo($lastOnline);

    // Save last login
    $db = new SQLite3('mydb.sq3');

    $stmt = $db -> prepare("UPDATE users SET lastOnline = :lastOnline WHERE userId = :userId");
    $stmt -> bindValue(':lastOnline', $lastOnline, SQLITE3_TEXT);
    $stmt -> bindValue(':userId', $userId, SQLITE3_TEXT);
    $result = $stmt -> execute();

    unset($db); // delete variable and free space for usage 

    // Unset session variables
    $_SESSION = array();

    // Erase session cookie from the browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"],
            $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    // End session on server side.
    session_destroy();

    // Redirect to login or home page
    header("Location: http://localhost/projects/Project/src/login_page.php");
    exit;
?>