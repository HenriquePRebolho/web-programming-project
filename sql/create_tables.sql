CREATE TABLE IF NOT EXISTS users (
    userId      INT PRIMARY KEY AUTO_INCREMENT,
    email       VARCHAR(50)  NOT NULL UNIQUE,
    surname     VARCHAR(50)  NOT NULL,
    password    VARCHAR(50)  NOT NULL,
    highScore   INT          NOT NULL DEFAULT 0,
    lastOnline  VARCHAR(50),
    isOnline    TINYINT      DEFAULT 0,
    lastSeen    INT          DEFAULT 0;
    screenWidth INT,
    screenHeight INT,
    opSys       VARCHAR(64),
    twofaCode   VARCHAR(255),
    changePassword TINYINT   DEFAULT 1
);

CREATE TABLE IF NOT EXISTS password_resets (
    tokenId   INT PRIMARY KEY AUTO_INCREMENT,
    userId    INT  NOT NULL,
    token     TEXT NOT NULL,
    expiresAt INT  NOT NULL,
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
);