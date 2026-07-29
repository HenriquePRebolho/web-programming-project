# Web Programming Project

A small web application with user accounts and a Spider-Man-inspired infinite runner game. After logging in, players use a web shot to hit blocks hanging from the ceiling and pull themselves forward while avoiding obstacles.

## Features

- Register, login and logout
- Two-factor authentication (2FA)
- Forgotten-password and password-change flow
- Logged-in home page with welcome message
- Online-user counter and high-score table
- Infinite runner game made with Three.js
- Background music and a mute button

## Built with

- HTML, CSS and JavaScript
- AJAX / jQuery
- PHP and MySQL
- Three.js
- XAMPP (Apache and MySQL)

## Project structure

- `src/` - PHP pages, styles, server-side code and the game
- `src/game/` - Three.js game files
- `sql/create_tables.sql` - database tables
- `assets/` - audio and font files
- `extern/` - external libraries, including the Google Authenticator code

## Getting started

1. Install and start XAMPP with **Apache** and **MySQL** running.
2. Put this project inside XAMPP's `htdocs` folder (or configure Apache to serve this folder).
3. Create a MySQL database named `mydb`.
4. Import `sql/create_tables.sql` into that database.
5. Check the database settings in `src/php/db.php`. The default values use XAMPP's usual `root` user with an empty password.
6. Open `src/login_page.php` in the browser through your local server, for example:

   `http://localhost/projects/Project/src/login_page.php`

## How to play

Log in, press **Play**, then click to shoot a web. Hit the blocks hanging from the ceiling to move forward and avoid crashing into the obstacles. Try to get the best score.

## Notes

This is a school web-programming project and is intended to run locally with XAMPP.
