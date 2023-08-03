DROP TABLE IF EXISTS `field_group`;

CREATE TABLE `field_group` (
  `field_group_id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `status` tinyint NOT NULL,
  `sort_order` int NOT NULL,
  PRIMARY KEY (`field_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
