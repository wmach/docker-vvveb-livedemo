DROP TABLE IF EXISTS `field_value_content`;

CREATE TABLE `field_value_content` (
  `field_value_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`field_value_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
