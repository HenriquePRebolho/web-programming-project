DROP TABLE IF EXISTS sq3.users;

CREATE TABLE IF NOT EXISTS main.users(
    userId INTEGER PRIMARY KEY,
    email VARCHAR (50) NOT NULL UNIQUE,
    surname VARCHAR (50) NOT NULL,
    password VARCHAR (50) NOT NULL,
    highScore INT NOT NULL DEFAULT(0),
    lastOnline VARCHAR,
    isOnline INTEGER DEFAULT(0),
    screenWidth INT,
    screenHeight INT,
    opSys VARCHAR(64),
    twofaCode VARCHAR,
    changePassword INTEGER DEFAULT (1)
);
