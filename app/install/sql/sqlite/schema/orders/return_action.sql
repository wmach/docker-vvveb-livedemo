DROP TABLE IF EXISTS `return_action`;

CREATE TABLE `return_action` (
`return_action_id`  INTEGER PRIMARY KEY AUTOINCREMENT,
`language_id` INT  NOT NULL DEFAULT '0',
`name` TEXT NOT NULL
-- PRIMARY KEY (`return_action_id`,`language_id`)
);





