<?php
    // Server side
    require_once __DIR__ . '/db.php';
    $stmt = $db->prepare("SELECT COUNT(*) AS usersOnline FROM users WHERE isOnline = 1 AND lastSeen > ?");
    $threshold = time() - 60; // offline if no ping in 60 seconds
    $stmt->bind_param('i', $threshold);
    $stmt->execute();
    $result = $stmt->get_result();
    $usersOnline = $result->fetch_assoc();
    $db->close();
    
    echo("<br> <p>Users online: ". $usersOnline['usersOnline'] ."</p>");

    return;
?>

