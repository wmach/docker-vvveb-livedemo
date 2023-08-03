--DROP SEQUENCE IF EXISTS message_seq;
CREATE SEQUENCE IF NOT EXISTS message_seq;


CREATE TABLE IF NOT EXISTS message (
  "message_id" int check ("message_id" > 0) NOT NULL DEFAULT NEXTVAL ('message_seq'),
  "type" varchar(20) NOT NULL DEFAULT 'message',
  "data" text DEFAULT NULL,
  "meta" text DEFAULT NULL,
  "date_added" timestamp(0) NOT NULL DEFAULT '2022-05-01 00:00:00',
  "date_modified" timestamp(0) NOT NULL DEFAULT '2022-05-01 00:00:00',
  PRIMARY KEY ("message_id")
);

CREATE INDEX "message_type_status_date" ON message ("type","date_added","message_id");
