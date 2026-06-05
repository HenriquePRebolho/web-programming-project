<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>New Password</title>

        <!-- CSS -->
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">
    </head>

    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white; font-family:Arial, Helvetica, sans-serif">Scan 2FA and set new password</h1>

        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">

            <?php
                session_start();

                if (!isset($_SESSION['user_id'])) {
                    header("Location: http://localhost/projects/Project/src/login_page.php");
                }

                $userId = $_SESSION['user_id'];

                // Redirect if user changePassword is 0
                require_once __DIR__ . '/php/db.php';
                $stmt = $db -> prepare("SELECT changePassword FROM users WHERE userId = ?");
                $stmt -> bind_param('i', $userId);
                $stmt -> execute();
                $result = $stmt ->get_result();
                $row = $result->fetch_assoc();
                if ($row["changePassword"] == 0) {
                    header("Location: http://localhost/projects/Project/src/login_page.php");
                }


                require_once '../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';
                $ga = new PHPGangsta_GoogleAuthenticator();
                
                $randomSecret = $ga->createSecret();

                // Ensuring unique 2FA code
                while (true)  {
                    $stmt = $db -> prepare("SELECT COUNT(*) as count FROM users WHERE twofaCode = ?");
                    $stmt -> bind_param('s', $randomSecret);
                    $stmt->execute();
                    $result = $stmt ->get_result();
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 0) {
                        break;
                    }
                    $randomSecret = $ga->createSecret();
                }
                $db->close();
                $qrCodeUrlBlob2 = $ga->getQRCodeGoogleUrl('Blog', $randomSecret);

                echo "<img style='display: block;-webkit-user-select: none;margin: auto;background-color: hsl(0, 0%, 90%);transition: background-color 300ms;' src='https://api.qrserver.com/v1/create-qr-code/?data=otpauth%3A%2F%2Ftotp%2FBlog%3Fsecret%3D".$randomSecret."&amp;size=200x200&amp;ecc=M'>";
                echo "<br>";
            ?>

            <form action="php/first_login.php" method="POST" onsubmit="getDeviceInfo()">
                
                <div class="mb-3">
                    <label>New Password</label> <br>
                    <input type="password" name="password" id="password" required minlength="9">
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="width" id="width">
                <input type="hidden" name="height" id="height">
                <input type="hidden" name="os" id="os">
                <input type="hidden" name="twofa" id="twofa">
                
                <div class="d-flex justify-content-center mb-1">
                    <button type="submit" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">
                        New Password
                    </button>
                </div>
            </form>

            <div id="sent" class="mt-1"></div>
        </div>

        <script type="text/javascript">
            function getDeviceInfo() {
                document.getElementById("width").value = screen.width; 
                document.getElementById("height").value = screen.height; 
                document.getElementById("os").value = window.navigator.platform;
                document.getElementById("twofa").value = "<?php echo htmlspecialchars($randomSecret); ?>";
                return true;
            }

            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault(); // stop normal form submission
                
                getDeviceInfo();

                const formData = new FormData(this);
                const errorDiv = document.getElementById('sent');
                errorDiv.textContent = ''; // clear previous errors

                fetch('php/first_login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect; // redirect if correct login
                    } else {
                        errorDiv.textContent = data.message;  // show error in page
                        errorDiv.style.color = 'var(--red-color)';
                    }
                })
                .catch(error => {
                    errorDiv.textContent = 'An unexpected error occurred.';
                    errorDiv.style.color = 'red';
                });
            });
        </script>
    </body>
<html>