DROP TABLE IF EXISTS `field_group_content`;

CREATE TABLE `field_group_content` (
`field_group_content_id` int NOT NULL,
`language_id` int NOT NULL,
`name` TEXT NOT NULL,
PRIMARY KEY (`field_group_content_id`,`language_id`)
);





