<?php

    if (isset($_GET['email']) && isset($_GET['surname'])) {

        // Extract user info
        $email = $_GET["email"];
        $surname = $_GET["surname"];

        // Analyze email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo("Could not register: '" . $email . "' is not a valid email");
            return;
        }

        // Inform if user already exists with that email
        $db = new SQLite3('mydb.sq3');
        $sql = "SELECT * FROM users WHERE email = '" .$email . "'";
        $result = $db -> query($sql);
        if ($result -> fetchArray(SQLITE3_ASSOC)) {
            echo("Email already registered.");
                return;
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
        $sql = "INSERT INTO users (email, surname, password) VALUES ('".$email ."','".$surname."','".$hashed_password."')";
        $result = $db -> query($sql);
        if (!$result) {
            echo("Could not create.");
            return;
        }
        unset($db); // delete variable and free space for usage 


        // Define subject // TODO: change Webshop
        $subject = "Webshop - Login details";

        // Define html message // TODO: change "Webshop"
        $message = "<h1>Welcome to Webshop!</h1>
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
        if($success) {
            echo("Confirmation email sent successfully. <a href=http://localhost/projects/Project/src/login_page.php>Back to login page</a>");
            return;
            } else {
            echo($errorMessage = error_get_last()['message']);
            return;
        }
    } else {
        echo("Could not get all needed information");
    } 
?>