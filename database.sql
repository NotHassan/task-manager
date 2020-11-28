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
(1, 'Task 1', 'Task 1 Example Description'),
(2, 'Task 2', 'Task 2 Example Description'),
(3, 'Task 3', 'Task 3 Example Description'),
(4, 'Task 4', 'Task 4 Example Description'),
(5, 'Task 5', 'Task 5 Example Description'),
(6, 'Task 6', 'Task 6 Example Description'),
(7, 'Task 7', 'Task 7 Example Description');


DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
