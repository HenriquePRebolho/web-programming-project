<?php
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        $db = new SQLite3('mydb.sq3');
        
        $stmt = $db -> prepare ("SELECT email FROM users WHERE email = :email");
        $stmt -> bindValue(':email', $email, SQLITE3_TEXT);
        
        $result = $stmt -> execute();
        
        $user = $result -> fetchArray(SQLITE3_ASSOC);

        if ($user) {
            echo("<b id='alreadyRegisterMsg'>Email already registered.</b>");
        }

        unset($db);
    }
?>
