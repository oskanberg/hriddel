
DROP TABLE IF EXISTS `authorship`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `articles` (
  `a_id` int NOT NULL AUTO_INCREMENT,
  `contents` text(30000) NOT NULL,
  `status` varchar(16) NOT NULL,
  `title` varchar(250) NOT NULL,
  `publish_date` date NOT NULL,
  `type` varchar(7) NOT NULL,
  `cover_image` varchar(100),
  PRIMARY KEY (`a_id`)
);

CREATE TABLE `users` (
  `u_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) UNIQUE NOT NULL,
  `name` varchar(50),
  `type` varchar(10),
  PRIMARY KEY (`u_id`)
);

CREATE TABLE `authorship` (
  `auth_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `a_id` int NOT NULL,
  PRIMARY KEY (`auth_id`),
  FOREIGN KEY (u_id) REFERENCES users(u_id),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);


INSERT INTO articles (contents, status, title, publish_date, type, cover_image) 
VALUES ('Lorem Ipsum', 'submitted', 'Test', NOW(), 'article', 'dsfjasdflka.jpg');

INSERT INTO users (username, name, type) VALUES ('testman', 'Test Man', 'editor');

INSERT INTO authorship (u_id, a_id) VALUES (1, 1);