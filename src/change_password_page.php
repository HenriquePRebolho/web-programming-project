<?php
    $token = $_GET['token'] ?? '';
    $error = '';

    $db = new SQLite3('php/mydb.sq3');
    $stmt = $db -> prepare("SELECT userId, expiresAt FROM password_resets WHERE token = :token");
    $stmt -> bindValue(":token", $token, SQLITE3_TEXT);
    $result = $stmt -> execute();
    $tokenInfo = $result -> fetchArray(SQLITE3_ASSOC);

    if (!$tokenInfo || $tokenInfo['expiresAt'] < time()) {
        die("Invalid or expired link.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $new_password = $_POST['password'];
        
        // Check if password is valid
        // TODO: make errors appear in login_page.php and not in login.php
        $upperCase = preg_match('/[A-Z]/', $new_password); 
        $lowerCase = preg_match('/[a-z]/', $new_password); 
        $numericVal = preg_match('/[0-9]/', $new_password);
        if (!($upperCase && $lowerCase && $numericVal && strlen($new_password))) {
            die("Password not valid. Must be at least 9 characters, one upper case letter, one lower case letter and one number");
        } 
        else {
            $hashed_new_password = hash("sha512", $new_password);
            $stmt = $db -> prepare("UPDATE users 
                        SET password = :new_password
                        WHERE userId = :userId");
            $stmt -> bindValue(':new_password', $hashed_new_password, SQLITE3_TEXT);
            $stmt -> bindValue(':userId', $tokenInfo['userId'], SQLITE3_INTEGER);
            $result = $stmt -> execute();
            if (!$result) {
                die("Could not update password.");
            }

            // Delete token after use
            $stmt = $db -> prepare("DELETE FROM password_resets WHERE token = :token");
            $stmt  ->bindValue(":token", $token, SQLITE3_TEXT);
            $stmt -> execute();

            unset($db);

            echo "Password updated! <a href='login_page.php'>Log in</a>";
            exit;
        }
    }

?>


<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Change password</title>

        <!-- CSS -->
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">       
    </head>

    
    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white;">Reset your password</h1>

        <!-- TODO: fix css in form box -->
        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
            <form method="POST">
                
                <div class="mb-3">
                    <label>New Password</label> <br>
                    <input type="password" name="password" id="password" required minlength="9">
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="d-flex justify-content-center mb-1">
                    <button type="submit" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Change password</button>
                </div>
            </form>

            <!-- TODO: make errors appear in forgot_password_page.php and not in forgot_password.php -->    
            <div id="sent"></div>
        </div>
    </body>
</html>