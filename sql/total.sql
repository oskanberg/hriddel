
DROP TABLE IF EXISTS authorship;
DROP TABLE IF EXISTS column_mappings;
DROP TABLE IF EXISTS reader_comments;
DROP TABLE IF EXISTS editor_comments;
DROP TABLE IF EXISTS review_scores;
DROP TABLE IF EXISTS likes_and_dislikes;
DROP TABLE IF EXISTS columns;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS users;

CREATE TABLE articles (
  a_id int NOT NULL AUTO_INCREMENT,
  contents text(30000) NOT NULL,
  status enum ('submitted', 'under review', 'awaiting changes', 'published', 'rejected') NOT NULL,
  title varchar(250) NOT NULL,
  publish_date date NOT NULL,
  type enum ('article', 'column article', 'review') NOT NULL,
  cover_image varchar(100),
  PRIMARY KEY (a_id)
);

CREATE TABLE users (
  username varchar(20) UNIQUE NOT NULL,
  name varchar(50),
  type enum ('subscriber', 'writer', 'editor', 'publisher'),
  PRIMARY KEY (username)
);

CREATE TABLE authorship (
  username varchar(20) NOT NULL,
  a_id int NOT NULL,
  PRIMARY KEY (a_id, username),
  FOREIGN KEY (username) REFERENCES users(username),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);

CREATE TABLE reader_comments (
  rc_id int NOT NULL AUTO_INCREMENT,
  username varchar(20) NOT NULL,
  a_id int NOT NULL,
  content varchar(1000) NOT NULL,
  PRIMARY KEY (rc_id),
  FOREIGN KEY (username) REFERENCES users(username),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);

CREATE TABLE editor_comments (
  ec_id int NOT NULL AUTO_INCREMENT,
  username varchar(20) NOT NULL,
  a_id int NOT NULL,
  content varchar(1000) NOT NULL,
  time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ec_id),
  FOREIGN KEY (username) REFERENCES users(username),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);

CREATE TABLE columns (
  name varchar(60) UNIQUE NOT NULL,
  PRIMARY KEY (name)
);

CREATE TABLE column_mappings (
  c_name varchar(60),
  a_id int NOT NULL,
  PRIMARY KEY (a_id),
  FOREIGN KEY (a_id) REFERENCES articles(a_id),
  FOREIGN KEY (c_name) REFERENCES columns (name)
);

CREATE TABLE review_scores (
  a_id int NOT NULL,
  score int NOT NULL,
  PRIMARY KEY (a_id),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);

CREATE TABLE likes_and_dislikes (
  username varchar(20) NOT NULL,
  a_id int NOT NULL,
  impression ENUM ('dislike', 'like'),
  PRIMARY KEY (username, a_id),
  FOREIGN KEY (a_id) REFERENCES articles(a_id)
);