<?php
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        require_once __DIR__ . '/db.php';
        
        $stmt = $db -> prepare ("SELECT email FROM users WHERE email = ?");
        $stmt -> bind_param('s', $email);
        $stmt -> execute();
        $result = $stmt ->get_result();
        $user = $result -> fetch_assoc();

        if ($user) {
            echo("<b id='alreadyRegisterMsg'>Email already registered.</b>");
        }

        $db->close();
    }
?>
