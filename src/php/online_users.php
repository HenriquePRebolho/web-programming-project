<?php
    // Server side
    $db = new SQLite3('mydb.sq3');
    $count = "count()";
    $stmt = $db -> prepare("SELECT COUNT(*) AS usersOnline FROM users WHERE isOnline = 1");
    $result = $stmt -> execute();
    $usersOnline = $result -> fetchArray(SQLITE3_ASSOC);
    
    echo("<br> Users online: ". $usersOnline['usersOnline'] ."<br>");

    unset($db);
    return;
?>

