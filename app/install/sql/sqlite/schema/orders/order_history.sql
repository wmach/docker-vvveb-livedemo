DROP TABLE IF EXISTS `order_history`;

CREATE TABLE `order_history` (
`order_history_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`order_id` INT  NOT NULL,
`order_status_id` INT  NOT NULL,
`notify` TINYINT NOT NULL DEFAULT '0',
`comment` text NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`order_history_id`)
);





