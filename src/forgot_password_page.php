<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Forgot password</title>

        <!-- CSS -->
        <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
        <link href="StyleSheet.css" rel="stylesheet">       
    </head>

    
    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

        <h1 class="m-1" style="color: white;">Reset your password</h1>

        <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
            <form action="php/send_change_password_email.php" method="POST">
                <div class="mb-3">
                    <label for="email">Email</label> <br>
                    <input type="email" name="email" id="email" placeholder="name@email.com" minlength="6">
                </div>

                <div class="d-flex justify-content-center mb-1">
                    <button type="submit" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Send email</button>
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

                fetch('php/send_change_password_email.php', {
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