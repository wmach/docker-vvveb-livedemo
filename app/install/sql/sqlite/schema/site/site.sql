DROP TABLE IF EXISTS `site`;

CREATE TABLE `site` (
`site_id` INTEGER PRIMARY KEY,
`key` TEXT NOT NULL,
`name` TEXT NOT NULL,
`host` TEXT NOT NULL,
`theme` TEXT NOT NULL,
`template` TEXT NOT NULL DEFAULT '',
`settings` TEXT NOT NULL DEFAULT ''
-- PRIMARY KEY (`site_id`)
);
