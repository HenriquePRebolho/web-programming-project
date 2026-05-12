DROP TABLE IF EXISTS game.users;

CREATE TABLE IF NOT EXISTS game.users(
    userId INTEGER AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR (50) NOT NULL UNIQUE, # email, min 5char+@
    # userName VARCHAR (50),
    password VARCHAR (50) NOT NULL,  # sha256
    highScore INT DEFAULT(0),
    lastOnline DATETIME NOT NULL,
    isOnline BOOlEAN NOT NULL,
    screenHeight INT NOT NULL, 
    screenWidth INT NOT NULL,
    opSys VARCHAR(64) NOT NULL,
    2facode varchar(30) NOT NULL,
);