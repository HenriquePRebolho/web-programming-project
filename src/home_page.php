<?php
    session_start();

    if (!isset($_SESSION['user_id'])) { // TODO: and qr code
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
            // Get amount of online users
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

            // Get best scores table
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

            // Get welcome message
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

        <!-- CSS -->
        <link href="..\extern\bootstrap\css\bootstrap-grid.min.css" rel="stylesheet">
        <link href="./StyleSheet.css" rel="stylesheet">

        <style>
            canvas {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 0;        /* behind everything */
            }

            body > *:not(canvas) {
                position: relative;
                z-index: 1;        /* above canvas */
            }
            body {
                text-align: center;
            }
        </style>
    </head>

    <body class="d-flex flex-column justify-content-center align-items-center vh-100 grey-color">
        <div id="welcome" class="mb-3"></div>

        <div class="mb-3">
            <form action="php/logout.php" method="POST" onsubmit="getDate()">
                <!-- Hidden field -->
                <input type="hidden" name="lastOnline" id="lastOnline">
                <div class="d-flex justify-content-center">
                    <button type="submit" id="logout" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Logout</button>
                </div>
            </form>
        </div>
        
        <div id=online_users class="mb-3"></div>

        <div id=best_scores class="mb-3"></div>

        <div class="d-flex justify-content-center mt-3">
            <button onclick="startGame()" id="play" class="py-2 red-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Play</button>
        </div>
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