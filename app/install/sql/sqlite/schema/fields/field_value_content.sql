DROP TABLE IF EXISTS `field_value_content`;

CREATE TABLE `field_value_content` (
`field_value_id` INTEGER NOT NULL,
`language_id` INTEGER NOT NULL,
`field_id` INTEGER NOT NULL,
`name` TEXT NOT NULL,
PRIMARY KEY (`field_value_id`,`language_id`)
);





