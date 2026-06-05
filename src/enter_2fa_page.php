<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="HenriqueRebolloPadovani">
    <title>2FA</title>

    <!-- CSS -->
    <link href="../extern/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet">
    <link href="StyleSheet.css" rel="stylesheet">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">

    <h1 class="m-1" style="color: white;">Login</h1>

    <div id="FormBox" class="d-flex flex-column justify-content-center align-items-center light-grey-color p-4">
    
        <form action="php/enter_2fa.php" method="POST">
        
            <div class="mb-3">
                <label>2FA code</label> <br>
                <input type="number" name="twofa" id="twofa" required>
            </div>
            
            <div class="d-flex justify-content-center mb-1">
                <button type="submit" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Enter</button>
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

            fetch('php/enter_2fa.php', {
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