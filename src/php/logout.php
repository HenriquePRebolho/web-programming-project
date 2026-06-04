<?php
    session_start();

    $userId = $_SESSION["user_id"];
    $lastOnline = $_POST["lastOnline"];
    echo($lastOnline);

    // Save last login
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("UPDATE users SET lastOnline = ? WHERE userId = ?");
    $stmt -> bind_param('si', $lastOnline, $userId);
    $stmt -> execute();

    if ($db->affected_rows === 0) {
        echo "Error in saving data.";
    }

    $db->close(); 

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