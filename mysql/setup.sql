-- This entire database can be constructed by running “source setup.sql” from mysql.
CREATE DATABASE `web_app` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

USE `web_app`;

GRANT ALL PRIVILEGES ON web_app.* TO 'the_user'@'localhost' IDENTIFIED BY 'the_password';

source file.sql;
source user.sql;
