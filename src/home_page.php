<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
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

        <script type="text/javascript">
            $( document ).ready(function() { // wait until page is loaded
                setInterval(function(){  // execute every 1s (param 2)
                    $.get("./php/best_scores.php",
                        {  }, // data passed to servers
                        function (data) {
                            $('#best_scores').html(data); 
                        }
                    );
                }, 1000); // speed update of 1s
            });
        </script>

        <script type="text/javascript">
            $.get("./php/welcome.php",
                {  }, // data passed to servers
                function (data) {
                    $('#welcome').html(data); 
                }
            );
        </script>
    </head>

    <body>
        <div id=welcome></div>

        <form action="php/logout.php" method="POST" onsubmit="getDate()">
            <!-- Hidden field -->
            <input type="hidden" name="lastOnline" id="lastOnline">
            <button type="submit"> Logout </button>
        </form>
        
        <div id=output></div>

        <div id=best_scores></div>
    </body>
</html>

<script>
    function getDate() {
        const weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        const d = new Date();
        let day = weekday[d.getDay()];

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = day + ' - ' + dd + '/' + mm + '/' + yyyy;

        document.getElementById("lastOnline").value = today;
    }
</script>