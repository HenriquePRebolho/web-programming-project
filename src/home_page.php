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

        <!-- CSS -->
        <link href="..\extern\bootstrap\css\bootstrap-grid.min.css" rel="stylesheet">
        <link href="./StyleSheet.css" rel="stylesheet">

        <script type="text/javascript">
            $( document ).ready(function() { // wait until page is loaded
                setInterval(function(){  // execute every 1s (param 2)
                    $.get("./php/online_users.php",
                        {  }, // data passed to servers
                        function (data) {
                            $('#online_users').html(data); 
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

        <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.176.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.176.0/examples/jsm/"
            }
        }
        </script>
        <script type="module" src="game/main.js"></script>
    </head>

    <body>
        <div id=welcome></div>

        <form action="php/logout.php" method="POST" onsubmit="getDate()">
            <!-- Hidden field -->
            <input type="hidden" name="lastOnline" id="lastOnline">
            <button type="submit" id="logout"> Logout </button>
        </form>
        
        <div id=online_users></div>

        <div id=best_scores></div>

        <button onclick="startGame()" id="play">Play</button>
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

    function startGame() {
        document.getElementById('welcome').style.display = 'none';
        document.getElementById('logout').style.display = 'none';
        document.getElementById('online_users').style.display = 'none';
        document.getElementById('play').style.display = 'none';
        window.onStartGame();
    }

    function gameOver(score) {
        document.getElementById('welcome').style.display = 'block';
        document.getElementById('logout').style.display = 'block';
        document.getElementById('online_users').style.display = 'block';
        document.getElementById('play').style.display = 'block';

        fetch('./php/save_score.php', {
            method: 'POST',
            body: new URLSearchParams({ score: score })
        });
    }

    window.onGameOver = function(score) {
        // restore UI + fetch save_score
        gameOver(score);
    }
</script>