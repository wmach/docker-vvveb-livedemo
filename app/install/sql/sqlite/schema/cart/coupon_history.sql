DROP TABLE IF EXISTS `coupon_history`;

CREATE TABLE `coupon_history` (
`coupon_history_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`coupon_id` INT  NOT NULL,
`order_id` INT  NOT NULL,
`user_id` INT  NOT NULL,
`quantity` decimal(15,4) NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
--PRIMARY KEY (`coupon_history_id`)
);





