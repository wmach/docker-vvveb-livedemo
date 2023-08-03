DROP TABLE IF EXISTS product_related;
CREATE TABLE product_related (
  "product_id" int check ("product_id" > 0) NOT NULL,
  "related_product_id" int check ("related_product_id" > 0) NOT NULL,
  PRIMARY KEY ("product_id","related_product_id")
);