DROP TABLE IF EXISTS "admin";

DROP SEQUENCE IF EXISTS admin_seq;
CREATE SEQUENCE admin_seq;

CREATE TABLE "admin" (
  "admin_id" int check ("admin_id" > 0) NOT NULL DEFAULT NEXTVAL ('admin_seq'),
  "username" varchar(60) NOT NULL DEFAULT '',
  "first_name" varchar(32) NOT NULL DEFAULT '',
  "last_name" varchar(32) NOT NULL DEFAULT '',
  "password" varchar(191) NOT NULL DEFAULT '',
  "email" varchar(100) NOT NULL DEFAULT '',
 "phone_number" varchar(32) NOT NULL DEFAULT '',
  "url" varchar(100) NOT NULL DEFAULT '',
  "registered" timestamp(0) NOT NULL DEFAULT '2022-05-01 00:00:00',
  "status" int check ("status" >= 0) NOT NULL DEFAULT 0,
  "display_name" varchar(250) NOT NULL DEFAULT '',
  "role_id" int check ("role_id" > 0) DEFAULT NULL,
  "token" varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY ("admin_id")
);

CREATE INDEX "admin_user" ON admin ("username");
CREATE INDEX "admin_email" ON admin ("email");
