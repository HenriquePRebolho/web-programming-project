<?php
    // db.php
    $host   = 'localhost';
    $dbname = 'mydb';
    $user   = 'root';   // XAMPP default
    $pass   = '';       // XAMPP default (empty)

    $db = new mysqli($host, $user, $pass, $dbname);

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
?>