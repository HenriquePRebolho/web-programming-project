<?php
    // TODO:
    $db = new SQLite3('mydb.sq3');
    if (isset($_GET['email'])) {
        $sql = "SELECT * FROM users WHERE email = '" .$_GET['email'] . "'";
        $result = $db -> query($sql);
        while ($row = $result -> fetchArray(SQLITE3_ASSOC)) {
            if ($row) {
                echo("Email already registered.");
                return;
            }
        }
    }
    unset($db);
?>
