DROP TABLE IF EXISTS `return_history`;

CREATE TABLE `return_history` (
`return_history_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`return_id` INT  NOT NULL,
`return_status_id` INT  NOT NULL,
`notify` TINYINT NOT NULL,
`comment` text NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`return_history_id`)
);





