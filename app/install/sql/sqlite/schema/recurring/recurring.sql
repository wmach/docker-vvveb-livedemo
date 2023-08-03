DROP TABLE IF EXISTS `recurring`;

CREATE TABLE `recurring` (
`recurring_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`price` decimal(10,4) NOT NULL,
`frequency` TEXT  NOT NULL,
`duration` INTEGER NOT NULL,
`cycle` INTEGER NOT NULL,
`trial_status` TINYINT NOT NULL,
`trial_price` decimal(10,4) NOT NULL,
`trial_frequency` TEXT  NOT NULL,
`trial_duration` INTEGER NOT NULL,
`trial_cycle` INTEGER NOT NULL,
`status` TINYINT NOT NULL,
`sort_order` INT  NOT NULL
-- PRIMARY KEY (`recurring_id`)
);





