DROP TABLE IF EXISTS `order_shipment`;

CREATE TABLE `order_shipment` (
`order_shipment_id` INTEGER PRIMARY KEY AUTOINCREMENT,
`order_id` INT  NOT NULL,
`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`shipping_courier_id` TEXT NOT NULL,
`tracking_number` TEXT NOT NULL
-- PRIMARY KEY (`order_shipment_id`)
);





