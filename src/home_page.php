<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        die("Not authenticated");
    }
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Home</title>
        <meta charset="utf-8">

        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <script type="text/javascript">
            $( document ).ready(function() { // wait until page is loaded
                setInterval(function(){  // execute every 1s (param 2)
                    $.get("./php/online_users.php",
                        {  }, // data passed to servers
                        function (data) {
                            $('#output').html(data); 
                        }
                    );
                }, 1000); // speed update of 1s
            });
        </script>
    </head>

    <body>
        <h1>Welcome!</h1>
        <div id=output></div>
    </body>
</html>
