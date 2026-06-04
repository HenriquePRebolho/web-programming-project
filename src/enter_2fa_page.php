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
    
    </div>

</body>