DROP TABLE IF EXISTS `subscription_status`;

CREATE TABLE `subscription_status` (
`subscription_status_id` INT  NOT NULL,
`language_id` INT  NOT NULL,
`name` TEXT NOT NULL,
PRIMARY KEY (`subscription_status_id`,`language_id`)
);





