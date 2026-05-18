
<form action="php/change_password.php" method="POST" onsubmit="getDeviceInfo()">

    <label>New Password:</label>
    <input type="password" name="password" id="password" required minlength="9">
    <input type="checkbox" onclick="passwordVisibility()">

    <!-- Hidden fields -->
    <input type="hidden" name="width" id="width">
    <input type="hidden" name="height" id="height">
    <input type="hidden" name="os" id="os">
    <input type="hidden" name="twofa" id="twofa">

    <button type="submit">
        Change Password
    </button>

</form>

<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        die("Not authenticated");
    }

    require_once '../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';
    $ga = new PHPGangsta_GoogleAuthenticator();
    
    $randomSecret = $ga->createSecret();

    // Ensuring unique 2FA code
    $db = new SQLite3(__DIR__ . '/php/mydb.sq3');
    while (true)  {
        $stmt = $db -> prepare("SELECT COUNT(*) as count FROM users WHERE twofaCode = :randomSecret");
        if ($stmt === false) {
            die("Database error: " . $db->lastErrorMsg());
        }
        $stmt -> bindValue(':randomSecret', $randomSecret, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row['count'] == 0) {
            break;
        }
        $randomSecret = $ga->createSecret();
    }
    unset($db);
    $qrCodeUrlBlob2 = $ga->getQRCodeGoogleUrl('Blog', $randomSecret);

    echo "<img style='display: block;-webkit-user-select: none;margin: auto;background-color: hsl(0, 0%, 90%);transition: background-color 300ms;' src='https://api.qrserver.com/v1/create-qr-code/?data=otpauth%3A%2F%2Ftotp%2FBlog%3Fsecret%3D".$randomSecret."&amp;size=200x200&amp;ecc=M'>";
    echo "<br>";
?>

<script>
    function passwordVisibility() {
        var password = document.getElementById("password")
        if (password.type === "password") {
            password.type = "text";
        } else {
            password.type = "password";
        }
    }

    function getDeviceInfo() {
        document.getElementById("width").value = screen.width; 
        document.getElementById("height").value = screen.height; 
        document.getElementById("os").value = window.navigator.platform;
        document.getElementById('twofa').value = "<?php echo"$randomSecret"?>>";
    }
</script>


