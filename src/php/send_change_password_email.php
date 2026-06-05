<?php
    
    header('Content-Type: application/json');

    if (!isset($_POST['email'])) {
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

    // Check if email is registered
    require_once __DIR__ . '/db.php';
    $stmt = $db -> prepare("SELECT userId, changePassword FROM users WHERE email = ?");
    $stmt -> bind_param('s', $email);
    $stmt -> execute();
    $result = $stmt ->get_result();
    $user = $result -> fetch_assoc();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Email not registered.']);
        exit();
    }
    
    if ($user['changePassword'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Login for the first time before you change your password.']);
        exit();
    }
    
    
    // Delete previous tokens
    $stmt = $db -> prepare("DELETE FROM password_resets WHERE userId = ?");
    $stmt  ->bind_param("i", $user['userId']);
    $stmt -> execute();

    // Define token
    $token = bin2hex(random_bytes(32));
    $expiresAt = time() + 30; // + 30s for quick demonstration that it expires, not the standard 
    
    // Insert new token
    $stmt =  $db -> prepare ("INSERT INTO password_resets (userId, token, expiresAt) VALUES (?, ?, ?)");
    $stmt -> bind_param('isi', $user['userId'], $token, $expiresAt);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Could not save token.']);
        exit();
    }

    if ($db->insert_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Insert appeared to succeed but no row was created.']);
        exit();
    }
    $db->close(); 

    // URL for changing password
    $reset_url = "http://localhost/projects/Project/src/change_password_page.php?token=$token";


    $subject = "Reset password details";
    // Define html message
    $message = "<h1>Access the following link to change your password</h1>
                <a href='$reset_url'>$reset_url</a>";
    
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
        echo json_encode(['success' => false, 'message' => 'Change password email sent successfully. Check your email inbox.']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error in sending mail']);
        exit();
    }

?>