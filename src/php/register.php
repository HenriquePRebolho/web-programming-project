<?php

    header('Content-Type: application/json');

    if (!isset($_POST['email']) || !isset($_POST['surname'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit();
    }

    // Check if email is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Not a valid email']);
        exit();
    }

    // Extract user info
    $email = $_POST["email"];
    $surname = $_POST["surname"];

    // Check if email is registered
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT * FROM users WHERE email = ?");
    $stmt -> bind_param('s', $email);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    if ($user) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit();
    }
       
    // Define password
    function random_password($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
    $password = random_password(9);
    $hashed_password = hash('sha512', $password);

    // Create user on database
    $stmt =  $db -> prepare ("INSERT INTO users (email, surname, password) VALUES (?, ?, ?)");
    $stmt -> bind_param('sss', $email, $surname, $hashed_password);
    $stmt -> execute();
    if ($db->affected_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Could not create.']);
        exit();
    }
    $db->close();

    $subject = "Web Shooter Web - Login details";

    // Define html message
    $message = "<h1>Welcome to Web Shooter Web!</h1>
                <p>In order to activate your account, use this temporary password:</p>"
                .$password;
    
    // Define headers: To send HTML mail, the Content-type header must be set
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'From: henrique.rebolho@gmail.com';


    // Send email
    $success = mail(
        to: $email,
        subject: $subject,
        message: $message,
        additional_headers:  implode("\r\n", $headers)
    );

    $returnMsg = ''; 
    
    if($success) {
        echo json_encode(['success' => true, 'redirect' => 'http://localhost/projects/Project/src/login_page.php']);
        exit();
    } else {
        echo(error_get_last()['message']);
    }
?>