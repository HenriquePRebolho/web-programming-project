<?php
    $token = $_GET['token'] ?? '';

    // POST handling — return JSON
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');

        $postToken = $_POST['token'] ?? '';

        require_once __DIR__ . '/php/db.php';
        $stmt = $db->prepare("SELECT userId, expiresAt FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $postToken);
        $stmt->execute();
        $tokenInfo = $stmt->get_result()->fetch_assoc();

        if (!$tokenInfo || $tokenInfo['expiresAt'] < time()) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired link.']);
            exit();
        }

        $new_password = $_POST['password'];
        $upperCase  = preg_match('/[A-Z]/', $new_password); 
        $lowerCase  = preg_match('/[a-z]/', $new_password); 
        $numericVal = preg_match('/[0-9]/', $new_password);
        if (!($upperCase && $lowerCase && $numericVal && strlen($new_password) >= 9)) {
            echo json_encode(['success' => false, 'message' => 'Password not valid. Must be at least 9 characters, one upper case, one lower case and one number.']);
            exit();
        }

        $hashed = hash("sha512", $new_password);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE userId = ?");
        $stmt->bind_param('si', $hashed, $tokenInfo['userId']);
        $stmt->execute();

        if ($db->affected_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Error updating password.']);
            exit();
        }

        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $postToken);
        $stmt->execute();
        $db->close();

        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully!',
            'redirect' => 'http://localhost/projects/Project/src/login_page.php'
        ]);
        exit();
    }

    // GET — validate token before showing the form
    require_once __DIR__ . '/php/db.php';
    $stmt = $db->prepare("SELECT userId, expiresAt FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $tokenInfo = $stmt->get_result()->fetch_assoc();

    if (!$tokenInfo || $tokenInfo['expiresAt'] < time()) {
        die("Invalid or expired link.");
    }
    $db->close();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Change password</title>
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">       
    </head>
    
    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white;">Reset your password</h1>

        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
            <form id="resetForm">
                <div class="mb-3">
                    <label>New Password</label><br>
                    <input type="password" name="password" id="password" required minlength="9">
                </div>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="d-flex justify-content-center mb-1">
                    <button type="submit" class="py-2 red-color" style="width:100%;border-style:hidden;border-radius:40px;color:white;box-shadow:1px 1px 1px black">
                        Change password
                    </button>
                </div>
            </form>
            <div id="sent" class="mt-2"></div>
        </div>

        <script>
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const sentDiv = document.getElementById('sent');
                sentDiv.textContent = '';

                fetch('', {          // empty string = POST to the same URL (preserving ?token=...)
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        sentDiv.style.color = 'green';
                        sentDiv.innerHTML = data.message + ' <a href="' + data.redirect + '">Go to login</a>';
                        document.getElementById('resetForm').style.display = 'none'; // hide form
                    } else {
                        sentDiv.style.color = 'red';
                        sentDiv.textContent = data.message;
                    }
                })
                .catch(() => {
                    sentDiv.style.color = 'red';
                    sentDiv.textContent = 'An unexpected error occurred.';
                });
            });
        </script>
    </body>
</html>