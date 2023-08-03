DROP TABLE IF EXISTS `field_content`;

CREATE TABLE `field_content` (
`field_id` int NOT NULL,
`language_id` int NOT NULL,
`name` TEXT NOT NULL,
PRIMARY KEY (`field_id`,`language_id`)
);





