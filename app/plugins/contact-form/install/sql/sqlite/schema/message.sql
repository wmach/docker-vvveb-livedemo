-- DROP TABLE IF EXISTS `message`;

CREATE TABLE IF NOT EXISTS `message` (
`message_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`type` TEXT NOT NULL DEFAULT 'publish',
`data` TEXT NOT NULL DEFAULT '',
`meta` TEXT NOT NULL DEFAULT '',
`date_added` datetime NOT NULL DEFAULT '2022-05-01 00:00:00',
`date_modified` datetime NOT NULL DEFAULT '2022-05-01 00:00:00'
-- PRIMARY KEY (`post_id`)
);
