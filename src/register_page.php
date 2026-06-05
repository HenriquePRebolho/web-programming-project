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

        <!-- Check if email is registered -->
        <script type="text/javascript">
            $( document ).ready(function() { // wait until page is loaded
                setInterval(function(){  // execute every 1s (param 2)
                    $.get("./php/check_email.php",
                        { email: document.getElementById("email").value }, // data passed to servers
                        function (data) {
                            $('#checkEmail').html(data); 
                        }
                    );
                }, 1000); // speed update of 1s
            });
        </script>       


        <!-- Deactivate button if email is registered -->
        <script type="text/javascript">
            $( document.getElementById('alreadyRegisterMsg') ).ready(function() { // wait msg is loaded
                setInterval(
                    function(){  // execute every 1s (param 2)
                        const emailWarning = document.getElementById('alreadyRegisterMsg');
                        console.log(emailWarning);

                        if (emailWarning) {
                            document.getElementById('registerBtn').disabled = true;
                            document.getElementById('registerBtn').style.background = '#3f3f3f';
                        } else {
                            document.getElementById('registerBtn').disabled = false;
                            document.getElementById('registerBtn').style.background = '#de0606';
                        }
                    }, 
                    1000); // speed update of 1s
            });    
        </script>


        <!-- CSS -->
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">
    </head>

    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white;">Register</h1>

        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
            <form action="php/register.php" method="POST">
                <div class="mb-3">
                    <label for="email">Email</label> <br>
                    <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6" required>
                    <div id="checkEmail"></div>
                </div>
                
                <div class="mb-3">
                    <label for="email">Surname</label> <br>
                    <input type="text" name="surname" id="surname" placeholder="Smith" required>
                </div>

                <div class="d-flex justify-content-center mb-2">
                    <button type="submit" id='registerBtn' class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Register</button>
                </div>

                <div class="mt-0">
                    Have an account?<a href="login_page.php" target="_self" style="font-size:11px; color: #007fd7">Login here</a>
                </div>
            </form>
            
            <div id="sent" class="mt-1"></div>
        </div>
   
        <script type="text/javascript">
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault(); // stop normal form submission

                const formData = new FormData(this);
                const errorDiv = document.getElementById('sent');
                errorDiv.textContent = ''; // clear previous errors

                fetch('php/register.php', {
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