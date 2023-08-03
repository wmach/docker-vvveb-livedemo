-- DROP TABLE IF EXISTS `message`;

CREATE TABLE IF NOT EXISTS `message` (
  `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'message',
  `data` text NOT NULL,
  `meta` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `type_status_date` (`type`,`date_added`,`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

