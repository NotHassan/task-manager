CREATE DATABASE IF NOT EXISTS `project`;
USE `project`;

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `tasks` (`id`, `name`, `description`) VALUES
(1, 'Task 1', 'Task 1 Example Descrition'),
(2, 'Task 2', 'Task 2 Example Descrition'),
(3, 'Task 3', 'Task 3 Example Descrition'),
(4, 'Task 4', 'Task 4 Example Descrition'),
(5, 'Task 5', 'Task 5 Example Descrition'),
(6, 'Task 6', 'Task 6 Example Descrition'),
(7, 'Task 7', 'Task 7 Example Descrition');


DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
