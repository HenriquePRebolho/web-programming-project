<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Login</title> 

        <!-- CSS -->
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">       
    </head>

    
    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white;">Login</h1>

        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
            <form action="php/login.php" method="POST" onsubmit="getDeviceInfo()">
                <div class="mb-3">
                    <label for="email">Email</label> <br>
                    <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6">
                </div>

                <div class="mb-3">
                    <label for="password">Password</label> <br>
                    <input type="password" name="password" id="password" placeholder="*********" minlength="">
                </div>
                
                <!-- Hidden fields -->
                <input type="hidden" name="width" id="width">
                <input type="hidden" name="height" id="height">
                <input type="hidden" name="os" id="os">

                <div class="d-flex justify-content-center mb-1">
                    <button type="submit" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Login</button>
                </div>
            </form>

            <hr style="width:100%;text-align:left;margin-left:0">
            
            <div>
                <a href="register_page.php" target="_self" style="font-size:11px; color: #007fd7">New? Register here</a>
            </div>

            <hr style="width:100%;text-align:left;margin-left:0">
            
            <div>
                <a href="forgot_password_page.php" target="_self" style="font-size:11px; color: #007fd7">Forgot password?</a>
            </div>
    
            <div id="sent" class="mt-1"></div>
        </div>

        <script type="text/javascript">
            function getDeviceInfo() {
                document.getElementById("width").value = screen.width; 
                document.getElementById("height").value = screen.height; 
                document.getElementById("os").value = window.navigator.platform;
                return true;
            }

            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault(); // stop normal form submission
                
                getDeviceInfo();

                const formData = new FormData(this);
                const errorDiv = document.getElementById('sent');
                errorDiv.textContent = ''; // clear previous errors

                fetch('php/login.php', {
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
</html>