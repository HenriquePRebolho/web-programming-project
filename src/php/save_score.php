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
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT highScore FROM users WHERE userId = ?");
    $stmt -> bind_param('i', $_SESSION['user_id']);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    if ($user['highScore'] < $score) {
        $stmt = $db -> prepare("UPDATE users 
                        SET highScore = ?
                        WHERE userId = ?");
        $stmt -> bind_param('ii', $score, $_SESSION['user_id']);
        $stmt -> execute();

        if ($db->affected_rows === 0) {
            echo "Error in saving data.";
        }
    }
    $db->close();
?>