DROP TABLE IF EXISTS `stock_status`;

CREATE TABLE `stock_status` (
`stock_status_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`language_id` INT  NOT NULL,
`name` TEXT NOT NULL
-- PRIMARY KEY (`stock_status_id`,`language_id`)
);


CREATE INDEX `stock_status_id` ON `stock_status` (`stock_status_id`,`language_id`);


