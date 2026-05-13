<?php
    
    // php email: https://www.php.net/manual/en/function.mail.php

    if (isset($_GET['email'])) {

        // Extract email
        $email = $_GET["email"];

        // TODO: create user on database

        // TODO: inform if user already exists with that email


        // TODO: Analyze email
        // ^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$


        // Define subject // TODO: change Webshop
        $subject = "Webshop - Login details";

        // Define password
        // TODO: random or not?
        $password = "Password123";


        // Define html message // TODO: change "Webshop"
        $message = "<h1>Welcome to Webshop!</h1>
                    <p>In order to activate your account use this temporary password:</p>"
                    .$password;
        
        // Define headers: To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';


        // Send email
        $success = mail(
            to: $email,
            subject: $subject,
            message: $message,
            additional_headers:  implode("\r\n", $headers)
        );
        if($success) {
            echo("Confirmation email sent successfully.");
        } else {
            echo($errorMessage = error_get_last()['message']);
        }
    } else {
        echo("Email is not set");
    }
?>