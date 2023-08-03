DROP TABLE IF EXISTS `product_related`;

CREATE TABLE `product_related` (
  `product_id` INT UNSIGNED NOT NULL,
  `related_product_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`,`related_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
