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
    $db = new SQLite3('mydb.sq3');
    $stmt = $db -> prepare("SELECT * FROM users WHERE email = :email");
    $stmt -> bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt -> execute();
    $user = $result -> fetchArray(SQLITE3_ASSOC);
    if (!$user) {
        die("Email not registered.");
    }

    // Check if email and password are matching
    $hashed_password = hash('sha512', $password);
    $stmt = $db -> prepare("SELECT * FROM users WHERE email = :email AND password = :hashed_password");
    $stmt -> bindValue(':email', $email, SQLITE3_TEXT);
    $stmt -> bindValue(':hashed_password', $hashed_password, SQLITE3_TEXT);
    $result = $stmt -> execute();
    $user = $result -> fetchArray(SQLITE3_ASSOC);
    if (!$user) {
        die("Wrong password or email");
    }

    // Save email and user id into session
    $_SESSION['user_id'] = $user['userId'];
    $_SESSION['email'] = $user['email'];
    
    // If first login, send to another page to update password 
    if ($user['changePassword'] == 1) {
        header("Location: http://localhost/projects/Project/src/change_password_page.php");
        exit();
    }

    // Update user info
    $stmt = $db -> prepare("UPDATE users 
                        SET screenWidth = :screenWidth,
                        screenHeight = :screenHeight,
                        opSys = :os,
                        isOnline = 1
                        WHERE email = :email");
    $stmt -> bindValue(':screenWidth', $width, SQLITE3_TEXT);
    $stmt -> bindValue(':screenHeight', $height, SQLITE3_TEXT);
    $stmt -> bindValue(':os', $os, SQLITE3_TEXT);
    $stmt -> bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt -> execute();

    unset($db);

    // Send to login page
    header("Location: http://localhost/projects/Project/src/enter_2fa_page.php");
    exit();
/* 
    if (isset($_GET['email']) && isset($_GET['password']) && isset($_GET['width']) && isset($_GET['height']) && isset($_GET['os'])) {
        
        // Extract user info
        $email = $_GET["email"];
        $password = $_GET["password"];
        $width = $_GET["width"];
        $height = $_GET['height'];
        $os = $_GET['os'];

        // Analyze email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo("Could not register: '" . $email . "' is not a valid email");
            return;
        }

        // Check if email is registered
        $db = new SQLite3('mydb.sq3');
        $sql = "SELECT * FROM users WHERE email = '" .$email . "'";
        $result = $db -> query($sql);
        if (!$result) {
            echo("Email not registered.");
            return;
        }
        while ($row = $result->fetchArray(SQLITE3_ASSOC)){
            if (!($row['email'] == $email || $row['password'] == hash('sha512', $password))) {
                echo("Wrong email or password.");
                return;
            } else {
                // Ask for new password and generate 2FA
                if (!$row['lastOnline']) {
                    echo("Please generate a new password:");
                    



                }



            }
        }
        


        unset($db); // delete variable and free space for usage 

    } else {
        echo("Could not get all needed information");
        return;
    } */
?>