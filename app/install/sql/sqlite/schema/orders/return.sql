DROP TABLE IF EXISTS `return`;

CREATE TABLE `return` (
`return_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`order_id` INT  NOT NULL,
`product_id` INT  NOT NULL,
`user_id` INT  NOT NULL,
`first_name` TEXT NOT NULL,
`last_name` TEXT NOT NULL,
`email` TEXT NOT NULL,
`phone_number` TEXT NOT NULL,
`product` TEXT NOT NULL,
`model` TEXT NOT NULL,
`quantity` INTEGER NOT NULL,
`opened` TINYINT NOT NULL,
`return_reason_id` INT  NOT NULL,
`return_action_id` INT  NOT NULL,
`return_status_id` INT  NOT NULL,
`comment` text NOT NULL,
`date_ordered` date NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
-- PRIMARY KEY (`return_id`)
);





