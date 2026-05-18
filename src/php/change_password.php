<?php 

    session_start();

    // Check if data is set
    if(!(isset($_SESSION['user_id']) || isset($_SESSION['email']))) {
        die("Not authenticated");
    }

    // Check if email is valid
    if(!(isset($_POST['password']) || isset($_POST['width']) || isset($_POST['height']) || isset($_POST['os']) || isset($_POST['twofa']))) {
        die("Missing information");
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
        die("Password not valid. Must be at least 9 characters, one upper case letter, one lower case letter and one number");
    }


    // Update password
    $db = new SQLite3('mydb.sq3');
    $hashed_new_password = hash("sha512", $new_password);
    $stmt = $db -> prepare("UPDATE users 
                        SET password = :new_password, 
                        changePassword = 0,
                        screenWidth = :screenWidth,
                        screenHeight = :screenHeight,
                        opSys = :os,
                        isOnline = 1,
                        twofaCode = :twofa
                        WHERE userId = :userId");
    $stmt -> bindValue(':new_password', $hashed_new_password, SQLITE3_TEXT);
    $stmt -> bindValue(':screenWidth', $width, SQLITE3_TEXT);
    $stmt -> bindValue(':screenHeight', $height, SQLITE3_TEXT);
    $stmt -> bindValue(':os', $os, SQLITE3_TEXT);
    $stmt -> bindValue(':twofa', $twofa, SQLITE3_TEXT);
    $stmt -> bindValue(':userId', $_SESSION['user_id'], SQLITE3_TEXT);

    $result = $stmt -> execute();

    unset($db);

    // Send user to home page
    header("Location: http://localhost/projects/Project/src/enter_2fa_page.php");
    exit();
?>