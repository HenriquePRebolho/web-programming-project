<?php
    if (!isset($_POST['email']) || !isset($_POST['surname'])) {
        die("Missing data");
    }

    // Check if email is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo("Could not register: '" . $_POST['email'] . "' is not a valid email");
        return;
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
        die("Email already registered.");
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
        echo "Could not create.";
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
        echo("Confirmation email sent successfully. <a href=http://localhost/projects/Project/src/login_page.php>Back to login page</a>");
    } else {
        echo(error_get_last()['message']);
    }
?>