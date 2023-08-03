DROP TABLE IF EXISTS `voucher_history`;

CREATE TABLE `voucher_history` (
`voucher_history_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`voucher_id` INT  NOT NULL,
`order_id` INT  NOT NULL,
`quantity` decimal(15,4) NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`voucher_history_id`)
);





