DROP DATABASE IF EXISTS spammy;
CREATE DATABASE IF NOT EXISTS spammy;

CREATE TABLE IF NOT EXISTS `spammy`.`emails` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `spammy`.`secret` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `flag` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

INSERT INTO  `spammy`.`secret` (flag) VALUES ('DCI{sqli_with_valid_email_is_possible}');

DROP USER IF EXISTS spam_user;
CREATE USER `spam_user` IDENTIFIED BY 'Scur62xK7mPXA3ps';
GRANT SELECT, INSERT ON spammy.emails TO 'spam_user';
GRANT SELECT on spammy.secret to 'spam_user';

FLUSH PRIVILEGES;