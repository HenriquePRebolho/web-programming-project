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

            body > *:not(canvas) :not(#GameMenuBox){
                position: relative;
                z-index: 2;        /* above canvas */
            }
            #GameMenuBox {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(232, 232, 232, 0.19);
                z-index: 1;
                pointer-events: none;
            }
            body {
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div id="GameMenuBox"></div>

        <div class="d-flex justify-content-between mb-5 mt-3 ms-3 me-3">
            <div class="d-flex flex-column mt-4">
                <form action="php/logout.php" method="POST" onsubmit="getDate()">
                    <!-- Hidden field -->
                    <input type="hidden" name="lastOnline" id="lastOnline">
                    <div class="d-flex justify-content-center">
                        <button type="submit" id="logout" class="py-2 grey-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Logout</button>
                    </div>
                </form>

                <div>
                    <button onclick="handleLoop()" id="muteBtn" style="padding: 0.5rem; margin: 2px; background-color: var(--light-grey-color); border-color: var(--blue-color); border-width: 2px; border-style: solid; -moz-border-radius: 20px; -webkit-border-radius: 20px; border-radius: 10px;">🔊</button>
                </div>
            </div>

            <div></div>

            <div id=online_users class="ms-4" style="font-family: 'myFont'"></div>

            <div id=best_scores></div>
        </div>

        <div class="d-flex flex-column justify-content-center align-items-center vh-100 mt-5">
            
            <div id="welcome" class="mb-3"></div>
            
            <div class="d-flex justify-content-center mb-3">
                <button onclick="startGame()" id="play" class="py-2 blue-color" style="width: 100%; border-style:hidden; -moz-border-radius: 10px;-webkit-border-radius: 10px; border-radius:40px; color:white; box-shadow: 1px 1px 1px black">Play</button>
            </div>
            
        </div>

        <audio id="myAudio" loop muted>
            <source src="../assets/audio/2019-12-11_-_Retro_Platforming_-_David_Fesliyan.mp3" type="audio/mpeg">
        </audio> 
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
        document.getElementById('muteBtn').style.display = 'none';
        document.getElementById('GameMenuBox').style.display = 'none';
        window.onStartGame();
    }

    function gameOver(score) {
        document.getElementById('welcome').style.display = 'block';
        document.getElementById('logout').style.display = 'block';
        document.getElementById('online_users').style.display = 'block';
        document.getElementById('play').style.display = 'block';
        document.getElementById('muteBtn').style.display = 'block';
        document.getElementById('GameMenuBox').style.display = 'block';

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

<script>
    var x = document.getElementById("myAudio");
    let starded = false;

    function handleLoop() {
        if (!starded) {
            x.play();
            starded = true;
        }
        x.muted = !x.muted;
        document.getElementById("muteBtn").textContent = x.muted ? "🔇" : "🔊";
    } 
</script>