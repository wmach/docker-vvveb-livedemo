DROP TABLE IF EXISTS `tax_class`;

CREATE TABLE `tax_class` (
`tax_class_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`name` TEXT NOT NULL,
`content` TEXT NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`tax_class_id`)
);





