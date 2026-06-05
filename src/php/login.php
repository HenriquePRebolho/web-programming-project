<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['width']) 
            || !isset($_POST['height']) || !isset($_POST['os'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit();
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Not a valid email']);
        exit();
    }

    $email    = $_POST['email'];
    $password = $_POST['password'];
    $width    = $_POST['width'];
    $height   = $_POST['height'];
    $os       = $_POST['os'];

    require_once __DIR__ . '/db.php';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Email not registered.']);
        exit();
    }

    $hashed_password = hash('sha512', $password);
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $hashed_password);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Wrong password or email.']);
        exit();
    }

    $_SESSION['user_id'] = $user['userId'];
    $_SESSION['email']   = $user['email'];

    if ($user['changePassword'] == 1) {
        echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/first_login_page.php']);
        exit();
    }

    $stmt = $db->prepare("UPDATE users SET screenWidth=?, screenHeight=?, opSys=?, isOnline=1 WHERE email=?");
    $stmt->bind_param('iiss', $width, $height, $os, $email);
    $stmt->execute();
    $db->close();

    echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/enter_2fa_page.php']);
    exit();
?>