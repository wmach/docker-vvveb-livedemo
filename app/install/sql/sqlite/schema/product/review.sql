DROP TABLE IF EXISTS `review`;

CREATE TABLE `review` (
`review_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`product_id` INT  NOT NULL,
`user_id` INT  NOT NULL,
`author` TEXT NOT NULL,
`content` text NOT NULL,
`rating` INTEGER NOT NULL,
`status` TINYINT NOT NULL DEFAULT '0',
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`date_modified` datetime NOT NULL
-- PRIMARY KEY (`review_id`)
);



CREATE INDEX `review_product_id` ON `review` (`product_id`);

