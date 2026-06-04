<?php 
    session_start();

    // Check if data is set
    if(!(isset($_SESSION['user_id']))) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    // Check if 2fa is set
    if(!isset($_POST['twofa'])) {
        die("Missing information");
    }

    // Extract user info
    $userId = $_SESSION['user_id'];
    $twofa = $_POST['twofa'];

    // Get user 2FA
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT * FROM users WHERE userId = ?");
    $stmt -> bind_param('i', $userId);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    $secret = $user['twofaCode'];
    

    require_once '../../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';
    $checkResult=false;

    $ga = new PHPGangsta_GoogleAuthenticator();
    $checkResult= $ga -> verifyCode($secret, $twofa, 1); //1=30sec

    if($checkResult){
        header("Location: http://localhost/projects/Project/src/home_page.php");
    } else {
        header("Location: ../enter_2fa_page.php");
    }

    $db->close();


/*     // Check 2FA
    $db = new SQLite3('mydb.sq3');
    $stmt = $db -> prepare("UPDATE users SET twofaCode = :twofa WHERE userId = :userId");
    $stmt -> bindValue(':userId', $_SESSION['user_id'], SQLITE3_TEXT);

    $result = $stmt -> execute();

    unset($db);

    // Send user to home page
    header("Location: http://localhost/projects/Project/src/home_page.php");
    exit(); */
?>


