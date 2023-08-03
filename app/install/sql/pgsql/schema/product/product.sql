DROP TABLE IF EXISTS product;

DROP SEQUENCE IF EXISTS product_seq;
CREATE SEQUENCE product_seq;


CREATE TABLE product (
  "product_id" int check ("product_id" > 0) NOT NULL DEFAULT NEXTVAL ('product_seq'),
  "model" varchar(64) NOT NULL,
  "sku" varchar(64) NOT NULL,
  "upc" varchar(12) NOT NULL,
  "ean" varchar(14) NOT NULL,
  "jan" varchar(13) NOT NULL,
  "isbn" varchar(17) NOT NULL,
  "mpn" varchar(64) NOT NULL,
  "location" varchar(128) NOT NULL,
  "stock_quantity" int NOT NULL DEFAULT 0,
  "stock_status_id" int check ("stock_status_id" >= 0) NOT NULL,
  "image" varchar(191) NOT NULL,
  "manufacturer_id" int check ("manufacturer_id" >= 0) NOT NULL DEFAULT 0,
  "vendor_id" int check ("vendor_id" >= 0) NOT NULL DEFAULT 0,
  "shipping" smallint NOT NULL DEFAULT 1,
  "price" decimal(15,4) NOT NULL DEFAULT 0.0000,
  "points" int NOT NULL DEFAULT 0,
  "tax_class_id" int check ("tax_class_id" >= 0) NOT NULL,
  "date_available" date NOT NULL DEFAULT '1000-01-01',
  "weight" decimal(15,8) NOT NULL DEFAULT 0.00000000,
  "weight_class_id" int check ("weight_class_id" >= 0) NOT NULL DEFAULT 0,
  "length" decimal(15,8) NOT NULL DEFAULT 0.00000000,
  "width" decimal(15,8) NOT NULL DEFAULT 0.00000000,
  "height" decimal(15,8) NOT NULL DEFAULT 0.00000000,
  "length_class_id" int check ("length_class_id" >= 0) NOT NULL DEFAULT 0,
  "subtract" smallint NOT NULL DEFAULT 1,
  "minimum" int check ("minimum" >= 0) NOT NULL DEFAULT 1,
  "sort_order" int check ("sort_order" >= 0) NOT NULL DEFAULT 0,
  "status" smallint NOT NULL DEFAULT 0,
  "type" varchar(20) NOT NULL DEFAULT 'product',
  "template" varchar(191) NOT NULL DEFAULT '',
  "viewed" int NOT NULL DEFAULT 0,
  "date_added" timestamp(0) NOT NULL DEFAULT current_timestamp,
  "date_modified" timestamp(0) NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY ("product_id")
);

CREATE INDEX "product_type_status_date" ON product ("type","status","date_added","product_id");
