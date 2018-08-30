DROP DATABASE IF EXISTS sqli;
CREATE DATABASE IF NOT EXISTS sqli;

############# sqlitutobasics ######################

CREATE TABLE IF NOT EXISTS `sqli`.`users1` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `sqli`.`users2` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `sqli`.`users3` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);


INSERT INTO sqli . users1 (username, password)
VALUES('admin', 'supers3cr3t');
INSERT INTO sqli . users1 (username, password)
VALUES('guest', '123456789');

INSERT INTO sqli . users2 (id, username, password)
VALUES(1000, 'clone1', MD5('p4ssw0rd'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone2', MD5('bad_password'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone3', MD5('123456789'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone4', MD5('apple'));
INSERT INTO sqli . users2 (username, password)
VALUES('admin', MD5('2yv6sMMJ5zfHtMgw'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone5', MD5('ilikefood'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone6', MD5('stop spying'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone6', MD5('sqli ftw'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone7', MD5('hello world'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone8', MD5('987654321'));
INSERT INTO sqli . users2 (username, password)
VALUES('clone9', MD5('last clone'));

INSERT INTO sqli . users3 (username, password)
VALUES('admin', SHA1('asdjm23128JASDJj23daksa213BFD1'));

DROP USER IF EXISTS sqli_1;
DROP USER IF EXISTS sqli_2;
DROP USER IF EXISTS sqli_3;

CREATE USER `sqli_1` IDENTIFIED BY 'Pr365KcH8nuWsHfU';
GRANT SELECT ON sqli . users1 TO 'sqli_1';

CREATE USER `sqli_2` IDENTIFIED BY 'nF766UNjt9UZ7way';
GRANT SELECT on sqli . users2 TO 'sqli_2';

CREATE USER `sqli_3` IDENTIFIED BY '9UD4eryuy4B7fD2W';
GRANT SELECT on sqli . users3 TO 'sqli_3';

################# sqlitutofilters ###########################

CREATE TABLE IF NOT EXISTS `sqli`.`users4` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `sqli`.`users5` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `sqli`.`users6` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `sqli`.`secret_j5k6MX6y` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `flag` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

INSERT INTO sqli . users4 (username, password)
VALUES('admin', SHA1('khhaonqwnWBNEinaisdi1_293293!'));

INSERT INTO sqli . users5 (username, password)
VALUES('admin', SHA1('OPlalaweq233737djfnf'));

INSERT INTO sqli . users6 (username, password)
VALUES('admin', "plaintextpassword");
INSERT INTO sqli . users6 (username, password)
VALUES('SecurityExpert', "hunter2");
INSERT INTO sqli . users6 (username, password)
VALUES('not-a-flag', "12345");
INSERT INTO sqli . users6 (username, password)
VALUES("The Proclaimers", "500miles");
INSERT INTO sqli . users6 (username, password)
VALUES("guest", "guest");

INSERT INTO sqli . secret_j5k6MX6y (flag)
VALUES("DCI{Filters_are_often_easy_to_bypass}");

DROP USER IF EXISTS sqli_4;
DROP USER IF EXISTS sqli_5;
DROP USER IF EXISTS sqli_6;

CREATE USER `sqli_4` IDENTIFIED BY 'qZA485Rt4RTn5Vga';
GRANT SELECT ON sqli . users4 TO 'sqli_4';

CREATE USER `sqli_5` IDENTIFIED BY 'eM63EH2BVEH2EUf6';
GRANT SELECT ON sqli . users5 TO 'sqli_5';

CREATE USER `sqli_6` IDENTIFIED BY '7g8nVRvJjC8nXzkW';
GRANT SELECT ON sqli . users6 TO 'sqli_6';
GRANT SELECT ON sqli . secret_j5k6MX6y TO 'sqli_6';

FLUSH PRIVILEGES;