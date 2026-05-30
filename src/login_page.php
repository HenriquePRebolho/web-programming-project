<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Login</title>

        <script type="text/javascript">
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
                return true;
            }
        </script>

        <!-- CSS -->
        <link href="..\extern\bootstrap\css\bootstrap-grid.min.css" rel="stylesheet">
        <link href="./StyleSheet.css" rel="stylesheet">
    </head>

    <body>
        <div style="width:100%; background-color:black" class="container text-center" >
            <h1>Game</h1>
    
            <form action="php/login.php" method="POST" onsubmit="getDeviceInfo()">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6">
                <br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="*********" minlength="">
                <br>
                <input type="checkbox" onclick="passwordVisibility()">
                <!-- Hidden fields -->
                <input type="hidden" name="width" id="width">
                <input type="hidden" name="height" id="height">
                <input type="hidden" name="os" id="os">

                <button type="submit">Login</button>
            </form>

            <a href="register_page.php" target="_self">New? Register here</a>
        </div>
    
        <!-- TODO: make errors appear in login_page.php and not in login.php -->
        <div id="sent"></div>      
    </body>
</html>