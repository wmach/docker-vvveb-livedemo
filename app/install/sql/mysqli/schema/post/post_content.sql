DROP TABLE IF EXISTS `post_content`;

CREATE TABLE `post_content` (
  `post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `language_id` INT UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL DEFAULT "",
  `slug` varchar(191) NOT NULL DEFAULT "",
  `content` longtext,
  `excerpt` text,
  `meta_keywords` varchar(191) NOT NULL DEFAULT "",
  `meta_description` varchar(191) NOT NULL DEFAULT "",
  PRIMARY KEY (`post_id`,`language_id`),
  KEY `slug` (`slug`),
  FULLTEXT `search` (`name`,`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
