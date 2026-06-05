<?php
    session_start();

    if (!isset($_SESSION['user_id'])) exit();

    require_once __DIR__ . '/db.php';
    $now = time();
    $stmt = $db->prepare("UPDATE users SET lastSeen = ?, isOnline = 1 WHERE userId = ?");
    $stmt->bind_param('ii', $now, $_SESSION['user_id']);
    $stmt->execute();
    $db->close();
?>