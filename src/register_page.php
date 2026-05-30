<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Register</title>

        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <script type="text/javascript">
            var requestObj = new XMLHttpRequest();

            function showEmailedConfirmed() {
                if (requestObj.readyState == 4) {
                    var serverAnswer = requestObj.responseText;
                    var displayConfirmation = document.getElementById("sent");

                    if (displayConfirmation != null) {
                        displayConfirmation.innerHTML = serverAnswer;
                    }
                }
            }

            function confirmEmail() {
                var email = document.getElementById("email").value;
                var surname = document.getElementById("surname").value;
                var width = screen.width; var height = screen.height; var os = window.navigator.platform;
                var url = "php/register.php?email="+email+"&surname="+surname;
                console.log(url);
                if (email != "" && requestObj) {
                    requestObj.onreadystatechange = showEmailedConfirmed;
                    requestObj.open("get", url, true) // method, url, asyncrhonous
                    requestObj.send(null);
                }
                return false;
            }
        </script>
    </head>

    <body>
        <form action="php/register.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6" required>
            <input type="text" name="surname" id="surname" placeholder="Smith" required>
            <input type="submit" value="Register">
        </form>
    
        <div id="sent"></div>
    </body>
</html>