DROP DATABASE IF EXISTS closed_source;
CREATE DATABASE IF NOT EXISTS closed_source;

CREATE TABLE IF NOT EXISTS `closed_source`.`wall_of_shame` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `image_path` VARCHAR(255) NOT NULL,
    `comment` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

DROP USER if EXISTS wall;

CREATE USER `wall` IDENTIFIED BY 'Zhu58JV5rJkpSZGa';
GRANT SELECT, INSERT ON closed_source . wall_of_shame TO 'wall';

FLUSH PRIVILEGES;