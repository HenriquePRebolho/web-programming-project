<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }
?>

<form action="php/enter_2fa.php" method="POST">
    <label>2FA code</label>
    <input type="number" name="twofa" id="twofa" required>
    <button type="submit">Submit</button>
</form>