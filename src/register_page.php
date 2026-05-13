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
            if (email != "" && requestObj) {
                requestObj.onreadystatechange = showEmailedConfirmed;
                requestObj.open("get", "php/register.php?email="+email, true) // method, url, asyncrhonous
                requestObj.send(null);
            }
            return false;
        }
        </script>
    </head>

    <body>
        <form id="register_form" name="register_form">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6">
            <input type="button" value="Register" onclick="confirmEmail()">
        </form>
    
        <div id="sent"></div>
    </body>
</html>