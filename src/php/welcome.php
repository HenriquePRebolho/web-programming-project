<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    $db = new SQLite3('mydb.sq3');
    $stmt = $db -> prepare("SELECT surname, lastOnline FROM users WHERE email = :email");
    $stmt -> bindValue(':email', $_SESSION["email"], SQLITE3_TEXT);
    $result = $stmt -> execute();
    $user = $result -> fetchArray(SQLITE3_ASSOC);

    $lastOnline = "";
    if ($user["lastOnline"]) {
        $lastOnline = "<p>You were last online on " . $user["lastOnline"] . "</p>";
    } else {
        $lastOnline = "<p>This is your first time online!</p>";
    }

    echo("<h1>Welcome Mr&nbsp/&nbspMrs &nbsp" . $user["surname"] . "!</h1>" . $lastOnline);

    unset($db);
?>
