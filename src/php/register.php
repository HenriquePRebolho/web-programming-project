<?php

    // php email: https://www.php.net/manual/en/function.mail.php

    // Extract email
    $email = $_POST['email'];


    // Analyze email
    // ^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$


    // Define password
    // TODO: random or not?
    $password = "Password123";


    // Caution: (Windows only) When PHP is talking to a SMTP server directly, 
    // if a full stop is found on the start of a line, it is removed.
    // To counter-act this, replace these occurrences with a double dot.
    // TODO:
    // $text = str_replace("\n.", "\n..", $text);
    $message = "<h1>Welcome to Webshop!</h1><p>In order to activate your account use this temporary password:</p>".$password;

    
    // Define headers
    // TODO: check if all of this is necessary
    $headers[] = 'From: henrique.rebolho@gmail.com' . "\r\n" .
    'Reply-To: henrique.rebolho@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    // To send HTML mail, the Content-type header must be set
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    // Additional headers
    $headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
    $headers[] = 'From: Birthday Reminder <birthday@example.com>';
    $headers[] = 'Cc: birthdayarchive@example.com';
    $headers[] = 'Bcc: birthdaycheck@example.com';


    // Send email
    mail(
        to: $email,
        subject: "Webshop - Login details",
        message: $message,
        additional_headers: implode("\r\n", $headers)
    );

    // TODO: add callback to register_page.php to inform email has been sent

?>