<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="HenriqueRebolloPadovani">
        <title>Register</title>
    </head>

    <body>
        <form method="POST" action="php/register.php">
            <label for="email">Email:</label>
            <input type="email" id="email" placeholder="name@email.com" minlength="6">
            <input type="submit" value="Register" onclick="confirmEmail()">
        </form>

        <div id="sent"></div>
    </body>
</html>