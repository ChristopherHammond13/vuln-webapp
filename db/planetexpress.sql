-- MySQL dump 10.11
--
-- to install this database, from a terminal, type:
-- mysql -u USERNAME -p -h SERVERNAME planetexpress < planetexpress.sql
--
-- Host: localhost    Database: simpsons
-- ------------------------------------------------------
-- Server version   5.0.45-log

DROP DATABASE IF EXISTS planetexpress;
CREATE DATABASE planetexpress;
USE planetexpress;

DROP TABLE IF EXISTS `user`;

-- Privilege Level
-- 0: No access
-- 1: Access to own stuff
-- 2: Admin access

CREATE TABLE `user` (
    id int UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email varchar(256) NOT NULL,
    firstname varchar(256) NOT NULL,
    lastname varchar(256) NOT NULL,
    password varchar(40) NOT NULL,
    privilege_level int UNSIGNED NOT NULL DEFAULT 1
);

DROP TABLE IF EXISTS `shipment`;
CREATE TABLE `shipment` (
    id int UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name varchar(256) NOT NULL,
    cost decimal(13,4) NOT NULL,
    destination varchar(256) NOT NULL,
    source varchar(256) NOT NULL,
    customer_id int UNSIGNED NOT NULL,
    date_dispatch DATETIME NOT NULL,
    date_arrival DATETIME,

    FOREIGN KEY (customer_id) REFERENCES `user`(id)
);

DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
    id int UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    description varchar(256) NOT NULL,
    cost decimal(13,4) NOT NULL,
    width int UNSIGNED NOT NULL,
    height int UNSIGNED NOT NULL,
    depth int UNSIGNED NOT NULL,
    shipment_id int UNSIGNED NOT NULL,
    weight int UNSIGNED NOT NULL,

    FOREIGN KEY (shipment_id) REFERENCES `shipment`(id)
);

DROP TABLE IF EXISTS `update`;
CREATE TABLE `update` (
    id int UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content varchar(256) NOT NULL,
    date_received DATETIME NOT NULL,
    shipment_id int UNSIGNED NOT NULL,

    FOREIGN KEY (shipment_id) REFERENCES `shipment`(id)
);
