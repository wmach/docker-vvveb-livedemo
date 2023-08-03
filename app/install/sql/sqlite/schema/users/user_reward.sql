DROP TABLE IF EXISTS `user_reward`;

CREATE TABLE `user_reward` (
`user_reward_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`user_id` INT  NOT NULL DEFAULT '0',
`order_id` INT  NOT NULL DEFAULT '0',
`content` text NOT NULL,
`points` INTEGER NOT NULL DEFAULT '0',
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`user_reward_id`)
);





