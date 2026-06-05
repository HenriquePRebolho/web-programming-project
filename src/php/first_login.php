<?php 
    session_start();
    header('Content-Type: application/json');

    // Check if data is set
    if(!(isset($_SESSION['user_id']) || isset($_SESSION['email']))) {
        echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/login_page.php']);
        exit();
    }

    // Check if email is valid
    if(!(isset($_POST['password']) || isset($_POST['width']) || isset($_POST['height']) || isset($_POST['os']) || isset($_POST['twofa']))) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit();
    }

    // Extract user info
    $width = $_POST["width"];
    $height = $_POST['height'];
    $os = $_POST['os'];
    $new_password = $_POST['password'];
    $twofa = $_POST['twofa'];

    // Check if password is valid
    // TODO: make errors appear in login_page.php and not in login.php
    $upperCase = preg_match('/[A-Z]/', $new_password); 
    $lowerCase = preg_match('/[a-z]/', $new_password); 
    $numericVal = preg_match('/[0-9]/', $new_password);
    if (!($upperCase && $lowerCase && $numericVal && strlen($new_password))) {
        echo json_encode(['success' => false, 'message' => 'Password not valid. Must be at least 9 characters, one upper case letter, one lower case letter and one number']);
        exit();
    }

    // Update password
    require_once __DIR__ . '/db.php';
    $hashed_new_password = hash("sha512", $new_password);
    $stmt = $db->prepare("UPDATE users 
                        SET password = ?,
                            changePassword = 0,
                            screenWidth = ?,
                            screenHeight = ?,
                            opSys = ?,
                            isOnline = 1,
                            twofaCode = ?
                        WHERE userId = ?"
    );
    $stmt->bind_param("siissi",
        $hashed_new_password,
        $width,
        $height,
        $os,
        $twofa,
        $_SESSION['user_id']
    );
    $stmt->execute();

    $db->close();

    // Send user to 2fa page
    echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/enter_2fa_page.php']);
    exit();
?>