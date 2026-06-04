<?php
    session_start();

    // Check if data is set
    if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['width']) 
            || !isset($_POST['height']) || !isset($_POST['os'])) {
        die("Missing data");
    }

    // Check if email is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo("Could not register: '" . $_POST['email'] . "' is not a valid email");
        return;
    }

    // Extract user info
    $email = $_POST["email"];
    $password = $_POST["password"];
    $width = $_POST["width"];
    $height = $_POST['height'];
    $os = $_POST['os'];

    
    // Check if email is registered
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT * FROM users WHERE email = ?");
    $stmt -> bind_param('s', $email);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    if (!$user) {
        die("Email not registered.");
    }

    // Check if email and password are matching
    $hashed_password = hash('sha512', $password);
    $stmt = $db -> prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt -> bind_param('ss', $email, $hashed_password);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    if (!$user) {
        die("Wrong password or email");
    }

    // Save email and user id into session
    $_SESSION['user_id'] = $user['userId'];
    $_SESSION['email'] = $user['email'];
    
    // If first login, send to another page to update password 
    if ($user['changePassword'] == 1) {
        header("Location: http://localhost/projects/Project/src/first_login_page.php");
        exit();
    }

    // Update user info
    $stmt = $db -> prepare("UPDATE users 
                        SET screenWidth = ?,
                        screenHeight = ?,
                        opSys = ?,
                        isOnline = 1
                        WHERE email = ?");
    $stmt -> bind_param('iiss', $width, $height, $os, $email);
    $result = $stmt -> execute();

    $db->close();

    // Send to login page
    header("Location: http://localhost/projects/Project/src/enter_2fa_page.php");
    exit();
?>