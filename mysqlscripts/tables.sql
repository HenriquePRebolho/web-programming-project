DROP DATABASE IF EXISTS gamedb;
CREATE DATABASE gamedb;
USE gamedb;

DROP TABLE IF EXISTS game.users;
CREATE TABLE IF NOT EXISTS game.users(
    userId INTEGER AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR (256) NOT NULL UNIQUE, # email, min 5char+@
    userName VARCHAR (256),
    Password VARCHAR (256) NOT NULL,
    highScore INT DEFAULT(0),
    lastOnline DATETIME NOT NULL,
    screenHeight INT NOT NULL, 
    screenWidth INT NOT NULL,
    opSys VARCHAR(64) NOT NULL,

);


DROP TABLE IF EXISTS game.products;
CREATE TABLE IF NOT EXISTS game.products( #coins, skins and shit

);


DROP TABLE IF EXISTS game.orders;
CREATE TABLE IF NOT EXISTS game.orders(
    orderId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT FOREIGN KEY (users.userId),
    productId INT FOREIGN KEY (products.productId),
);





CREATE TABLE IF NOT EXISTS ordersdb.orders (
	ID INTEGER AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(64) NOT NULL,
    ISIN VARCHAR(64) NOT NULL,
    Amount INTEGER NOT NULL,
    Price INTEGER NOT NULL,
    State INTEGER NOT NULL
);


 
