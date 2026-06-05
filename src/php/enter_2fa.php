<?php 
    session_start();
    
    header('Content-Type: application/json');

    // Check if data is set
    if(!(isset($_SESSION['user_id']))) {
        echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/login_page.php']);
        exit();
    }

    // Check if 2fa is set
    if(!isset($_POST['twofa'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit();
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

    $db->close();

    require_once '../../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';
    $checkResult=false;

    $ga = new PHPGangsta_GoogleAuthenticator();
    $checkResult= $ga -> verifyCode($secret, $twofa, 1); //1=30sec

    if($checkResult){
        echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/home_page.php']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Wrong code']);
        exit();
    }
?>


