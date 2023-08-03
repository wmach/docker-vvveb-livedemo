DROP TABLE IF EXISTS `field`;

CREATE TABLE `field` (
`field_id` INTEGER NOT NULL ,
`field_group_id` int NOT NULL,
`type` TEXT NOT NULL,
`value` text NOT NULL,
`status` tinyint NOT NULL,
`sort_order` int NOT NULL,
PRIMARY KEY (`field_id`)
);





