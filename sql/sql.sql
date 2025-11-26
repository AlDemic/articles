CREATE TABLE `ranks` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `name` varchar(55) NOT NULL,
  `approved_power` int(11) NOT NULL DEFAULT '0'
);

INSERT INTO `ranks` (`id`, `name`, `approved_power`) VALUES
(1, 'Simple User', 0),
(2, 'Moderator', 3),
(3, 'Administrator', 5);

CREATE TABLE users(
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nick varchar(55) NOT NULL,
    email varchar(128) NOT NULL,
    pass varchar(255) NOT NULL,
    avatar varchar(5) DEFAULT 0,
    rank int DEFAULT 1,
    FOREIGN KEY (rank) REFERENCES ranks(id) ON UPDATE CASCADE
);

CREATE TABLE a_catgries(
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
    name varchar(255) NOT NULL
);

INSERT INTO a_catgries (id, name) VALUES
(1, 'No category'),
(2, 'Science'),
(3, 'Cinema'),
(4, 'Animation'),
(5, 'Games');

CREATE TABLE articles(
    id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
    title varchar(128) NOT NULL,
    short_desc varchar(255) NOT NULL,
    full_desc text NOT NULL,
    ctgry int NOT NULL DEFAULT 1,
    FOREIGN KEY (ctgry) REFERENCES a_catgries(id) ON UPDATE CASCADE,
    article_status ENUM('moderation', 'approved', 'declined') NOT NULL,
    id_author int NOT NULL,
    FOREIGN KEY(id_author) REFERENCES users(id) ON UPDATE CASCADE,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    edited_at DATETIME DEFAULT NULL
);

CREATE TABLE moderation_decisions(
    id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_article int NOT NULL,
    FOREIGN KEY (id_article) REFERENCES articles(id) ON DELETE CASCADE,
    id_user int NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON UPDATE CASCADE,
    decision int NOT NULL,
    decision_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE a_comments(
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_article int NOT NULL,
    FOREIGN KEY (id_article) REFERENCES articles(id) ON DELETE CASCADE,
    id_user int NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON UPDATE CASCADE,
    msg varchar(512) NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP
);