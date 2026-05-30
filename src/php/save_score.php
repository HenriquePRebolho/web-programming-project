<?php
    session_start();

    // Check if data is set
    if(!(isset($_SESSION['user_id']) || isset($_SESSION['email']))) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    // Check score is set
    if(!isset($_POST['score'])) {
        die("Missing information");
    }

    $score = $_POST['score'];

    // Check if score > high score
    $db = new SQLite3('mydb.sq3');
    $stmt = $db -> prepare("SELECT highScore FROM users WHERE userId = :userId");
    $stmt -> bindValue(':userId', $_SESSION['user_id'], SQLITE3_TEXT);
    $result = $stmt -> execute();
    $user = $result -> fetchArray(SQLITE3_ASSOC);
    if ($user['highScore'] < $score) {
        $stmt = $db -> prepare("UPDATE users 
                        SET highScore = :highScore
                        WHERE userId = :userId");
        $stmt -> bindValue(':highScore', $score, SQLITE3_INTEGER);
        $stmt -> bindValue(':userId', $_SESSION['user_id'], SQLITE3_TEXT);

        $result = $stmt -> execute();
    }
    unset($db);
?>