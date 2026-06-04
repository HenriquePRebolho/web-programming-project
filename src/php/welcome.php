<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT surname, lastOnline FROM users WHERE email = ?");
    $stmt -> bind_param('s', $_SESSION["email"]);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();

    if (!$user) {
        die("Could not find user");
    }

    $lastOnline = "";
    if ($user["lastOnline"]) {
        $lastOnline = "<p>You were last online on " . $user["lastOnline"] . "</p>";
    } else {
        $lastOnline = "<p>This is your first time online!</p>";
    }

    echo("<h1>Welcome Mr&nbsp/&nbspMrs &nbsp" . $user["surname"] . "!</h1>" . $lastOnline);

    $db->close();
?>
