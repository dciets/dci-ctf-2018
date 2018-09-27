DROP DATABASE IF EXISTS flagbook_db;
CREATE DATABASE IF NOT EXISTS flagbook_db;

CREATE TABLE IF NOT EXISTS `flagbook_db`.`users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT "",
    `location` VARCHAR(255) NOT NULL DEFAULT "Canada",
    `profile_image` VARCHAR(255) DEFAULT "assets/images/profile.png",
    `role` INT NOT NULL DEFAULT 1,
    PRIMARY KEY(`id`)
);

INSERT INTO `flagbook_db`.`users` (username, password, location) VALUES("Zark Muckerberg", MD5("A23Jsdjjj21399kasdmwh123"), "USA");
INSERT INTO `flagbook_db`.`users` (username, password) VALUES("h4ck3rm4n", MD5("s3cret"));
INSERT INTO `flagbook_db`.`users` (username, password, location) VALUES("Zark's friend", MD5("A23Jsdjjj21399kasdmwh123"), "USA");

CREATE TABLE IF NOT EXISTS `flagbook_db`.`posts` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `owner_id` INT NOT NULL,
    `content` TEXT,
    `image_path` VARCHAR(255),
    `post_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `flagbook_db`.`messages` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `content` TEXT,
    `post_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`)
);

INSERT INTO `flagbook_db`.`messages` (sender_id, receiver_id, content) VALUES (1,1,"Is it weird that I talk to myself?");
INSERT INTO `flagbook_db`.`messages` (sender_id, receiver_id, content) VALUES (1,1,"Nah.");

CREATE TABLE IF NOT EXISTS `flagbook_db`.`friends` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user1_id` INT NOT NULL,
    `user2_id` INT NOT NULL,
    PRIMARY KEY(`id`)
);

INSERT INTO `flagbook_db`.`friends` (user1_id, user2_id) VALUES (1,3);

CREATE TABLE IF NOT EXISTS `flagbook_db`.`friend_requests` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `flagbook_db`.`private_info` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `info` TEXT,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `flagbook_db`.`init_controllers` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `init_data` TEXT,
    PRIMARY KEY(`id`)
);

INSERT INTO flagbook_db.init_controllers (init_data) VALUES("SGVyZSBpcyB5b3VyIGZsYWcgZm9yIGJlaW5nIGxvZ2dlZCBpbiBhcyBINGNrM3JtNG46IERDSXszM2MzYmMwYmY2ZmRiOWY2ZWQwYmNkNWVjOGZjZTM1OH0uIFRvIGRpc2FibGUgdGhpcyBwb3B1cCwgY2xpY2sgPGEgaHJlZj0nL2xvZ291dCc+aGVyZTwvYT4=");
INSERT INTO flagbook_db.init_controllers (init_data) VALUES("VGhpcyBpcyBhIGZsYWcgZm9yIGJlaW5nIGZyaWVuZCB3aXRoIFphcmsgTXVja2VyYmVyZzogRENJezI2YTQxZTZiNjNlNzkxNjg1YjNhZWRkYThjNGNkOWU1fS4gVG8gZGlzYWJsZSB0aGlzIHBvcHVwLCBjbGljayA8YSBocmVmPScvYXBpL2ZyaWVuZHM/YWN0aW9uPWRlY2xpbmUmZnJpZW5kX2lkPTEmcmVkaXJlY3QnPmhlcmU8L2E+");
INSERT INTO flagbook_db.init_controllers (init_data) VALUES("WW91IG1hbmFnZWQgdG8gcmVhZCBaYXJrIE11Y2tlcmJlcmcgbWVzc2FnZXMgdG8gaGltc2VsZiwgZ29vZCBqb2IhIEhlcmUgaXMgeW91ciBmbGFnOiBEQ0l7YzI3N2FmNmM5YjVmYTNlZTE1ZTE5NzA2OTQ3ZTM5OGN9LiBUbyBkaXNhYmxlIHRoaXMgcG9wdXAsIGNsaWNrIDxhIGhyZWY9Jy8nPmhlcmU8L2E+");

DROP USER IF EXISTS flagbook_user;
DROP USER IF EXISTS controller_user;
DROP USER IF EXISTS sqli_user;

CREATE USER `flagbook_user` IDENTIFIED BY '2Gr568WSNQPW7d8Y';
GRANT SELECT, INSERT, UPDATE ON flagbook_db.users TO 'flagbook_user';
GRANT SELECT, INSERT ON flagbook_db.posts TO 'flagbook_user';
GRANT SELECT, INSERT, DELETE ON flagbook_db.friends TO 'flagbook_user';
GRANT SELECT, INSERT, DELETE ON flagbook_db.friend_requests TO 'flagbook_user';
GRANT SELECT, INSERT, DELETE ON flagbook_db.private_info TO 'flagbook_user';
GRANT SELECT, INSERT ON flagbook_db.messages TO 'flagbook_user';

CREATE USER `sqli_user` IDENTIFIED BY '9Gr568WSNQPW7d8Y';
GRANT SELECT ON flagbook_db.users TO 'sqli_user';
GRANT SELECT ON flagbook_db.friends TO 'sqli_user';
GRANT SELECT ON flagbook_db.friend_requests TO 'sqli_user';

CREATE USER `controller_user` IDENTIFIED BY '6Gr568WSNQPW7d8Z';
GRANT SELECT ON flagbook_db.init_controllers TO `controller_user`;

FLUSH PRIVILEGES;