DROP TABLE IF EXISTS `voucher`;

CREATE TABLE `voucher` (
`voucher_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`order_id` INT  NOT NULL,
`code` TEXT NOT NULL,
`from_name` TEXT NOT NULL,
`from_email` TEXT NOT NULL,
`to_name` TEXT NOT NULL,
`to_email` TEXT NOT NULL,
`voucher_theme_id` INT  NOT NULL,
`message` text NOT NULL,
`quantity` decimal(15,4) NOT NULL,
`status` TINYINT NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`voucher_id`)
);