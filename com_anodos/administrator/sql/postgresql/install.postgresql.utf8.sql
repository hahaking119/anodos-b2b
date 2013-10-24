DROP TABLE IF EXISTS "#__anodos_currency_rate";
DROP TABLE IF EXISTS "#__anodos_price";
DROP TABLE IF EXISTS "#__anodos_currency";
DROP TABLE IF EXISTS "#__anodos_price_type";
DROP TABLE IF EXISTS "#__anodos_product_quantity";
DROP TABLE IF EXISTS "#__anodos_stock";
DROP TABLE IF EXISTS "#__anodos_vendor_synonym";
DROP TABLE IF EXISTS "#__anodos_category_synonym";
DROP TABLE IF EXISTS "#__anodos_updater";
DROP TABLE IF EXISTS "#__anodos_product";
DROP TABLE IF EXISTS "#__anodos_product_vat";
DROP TABLE IF EXISTS "#__anodos_partner";

CREATE TABLE IF NOT EXISTS "#__anodos_partner" (
	"id" serial8 NOT NULL,
	"name" varchar(255) NOT NULL DEFAULT '',
	"alias" varchar(255) NOT NULL DEFAULT '',
	"category_id" int REFERENCES "#__categories"("id"),
	"vendor" int NOT NULL DEFAULT '0',
	"distributor" int NOT NULL DEFAULT '0',
	"client" int NOT NULL DEFAULT '0',
	"competitor" int NOT NULL DEFAULT '0',
	"state" int NOT NULL DEFAULT '1',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	"hits" int8 NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_product_vat" (
	"id" serial NOT NULL,
	"vat" decimal(5,2) NOT NULL DEFAULT '18.00',
	PRIMARY KEY ("id")
);

INSERT INTO "#__anodos_product_vat" VALUES (DEFAULT, 18.00), (DEFAULT, 0.00);

CREATE TABLE IF NOT EXISTS "#__anodos_product" (
	"id" serial8 NOT NULL,
	"name" varchar(255) NOT NULL,
	"alias" varchar(255) NOT NULL DEFAULT '',
	"article" varchar(225) NOT NULL DEFAULT '',
	"full_name" text NOT NULL DEFAULT '',
	"category_id" int NOT NULL REFERENCES "#__categories"("id"),
	"vendor_id" int8 NOT NULL REFERENCES "#__anodos_partner"("id"),
	"vat_id" int NOT NULL DEFAULT '1' REFERENCES "#__anodos_product_vat"("id"),
	"state" int NOT NULL DEFAULT '1',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	"hits" int8 NOT NULL DEFAULT '0',
	"duble_of" int8,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_updater" (
	"id" serial NOT NULL,
	"partner_id" int8 REFERENCES "#__anodos_partner"("id"),
	"category_id" int REFERENCES "#__categories"("id"),
	"name" varchar(255) NOT NULL DEFAULT '',
	"alias" varchar(255) NOT NULL DEFAULT '',
	"state" int NOT NULL DEFAULT '0',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	"hits" int8 NOT NULL DEFAULT '0',
	"updated" timestamp NOT NULL DEFAULT '1980.001',
	"updated_by" int8 NOT NULL DEFAULT '0',
	"client" varchar(255) NOT NULL DEFAULT '',
	"login" varchar(255) NOT NULL DEFAULT '',
	"pass" varchar(255) NOT NULL DEFAULT '',
	"cookie" text NOT NULL DEFAULT '',
	"key" varchar(255) NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

INSERT INTO "#__anodos_updater" (
  "id",
  "name",
  "alias",
  "state",
  "ordering",
  "key" )
VALUES
(DEFAULT, 'Обновление курсов валют ЦБР', 'CBR', 1, 0, 'e33193701a19d5fe636be0880b34f649'),
(DEFAULT, 'Обновление из конфигуратора Fujitsu', 'Fujitsu', 1, 3, '00d3600a2bdd09cd5450648581568976'),
(DEFAULT, 'Обновление данных Treolan', 'Treolan', 1, 2, '174c6bbd46dc5fc516e126045dac4095'),
(DEFAULT, 'Обновление данных Merlion (Москва)', 'MerlionMsk', 1, 1, '00d3600a2bdd09cd5450648581568976'),
(DEFAULT, 'Обновление данных Merlion (Самара)', 'MerlionSmr', 1, 4, '589f5103767c025b8f2b48f549823fb1');

CREATE TABLE IF NOT EXISTS "#__anodos_category_synonym" (
	"id" serial8 NOT NULL,
	"partner_id" int8 NOT NULL REFERENCES "#__anodos_partner"("id"),
	"category_id" int REFERENCES "#__categories"("id"),
	"name" varchar(255) NOT NULL DEFAULT '',
	"state" int NOT NULL DEFAULT '1',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_vendor_synonym" (
	"id" serial8 NOT NULL,
	"partner_id" int8 REFERENCES "#__anodos_partner"("id"),
	"vendor_id" int8 REFERENCES "#__anodos_partner"("id"),
	"name" varchar(255) NOT NULL,
	"state" int NOT NULL DEFAULT '1',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_stock" (
	"id" serial8 NOT NULL,
	"name" varchar(255) NOT NULL,
	"alias" varchar(255) NOT NULL,
	"delivery_time_min" interval NOT NULL DEFAULT '4 days',
	"delivery_time_max" interval NOT NULL DEFAULT '10 days',
	"partner_id" int8 NOT NULL REFERENCES "#__anodos_partner"("id"),
	"category_id" int REFERENCES "#__categories"("id"),
	"state" int NOT NULL DEFAULT '1',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	"hits" int8 NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_product_quantity" (
	"product_id" int8 NOT NULL REFERENCES "#__anodos_product"("id"),
	"stock_id" int8 NOT NULL REFERENCES "#__anodos_stock"("id"),
	"created" timestamp NOT NULL,
	"version" int8  NOT NULL DEFAULT '0',
	"created_by" int8  NOT NULL DEFAULT '0',
	"quantity" DECIMAL(13,2) NOT NULL,
	"state" int NOT NULL DEFAULT '1',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8  NOT NULL,
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	PRIMARY KEY ("product_id", "stock_id", "created", "version")
);

CREATE TABLE IF NOT EXISTS "#__anodos_price_type" (
	"id" serial NOT NULL,
	"name" varchar(255) NOT NULL,
	"alias" varchar(255) NOT NULL,
	"state" int NOT NULL DEFAULT '1',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__anodos_currency" (
	"id" serial NOT NULL,
	"name" varchar(255) NOT NULL,
	"alias" char(3) NOT NULL,
	"name_html" varchar(255) NOT NULL,
	"state" int NOT NULL DEFAULT '1',
	"ordering" int8 NOT NULL DEFAULT '0',
	"description" text NOT NULL DEFAULT '',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	"metadata" text NOT NULL DEFAULT '',
	PRIMARY KEY ("id")
);

INSERT INTO "#__anodos_currency" (
	"name",
	"alias",
	"name_html",
	"state",
	"ordering")
VALUES
('Российский рубль', 'RUB', 'р.', '1', '1'),
('Американский доллар', 'USD', '&#36;', '1', '2'),
('Евро', 'EUR', '&#8364;', '1', '3');

CREATE TABLE IF NOT EXISTS "#__anodos_price" (
	"stock_id" int8 NOT NULL REFERENCES "#__anodos_stock"("id"),
	"product_id" int8 NOT NULL REFERENCES "#__anodos_product"("id"),
	"created" timestamp NOT NULL,
	"created_by" int8 NOT NULL DEFAULT '0',
	"price" DECIMAL(13,2) NOT NULL,
	"currency_id" int8 NOT NULL REFERENCES "#__anodos_currency"("id"),
	"price_type_id" int NOT NULL REFERENCES "#__anodos_price_type"("id"),
	"discount" DECIMAL(5,2) NOT NULL DEFAULT '0.0',
	"state" int NOT NULL DEFAULT '1',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	PRIMARY KEY ("stock_id", "product_id", "created")
);

CREATE TABLE IF NOT EXISTS "#__anodos_currency_rate" (
	"currency_id" int8 NOT NULL REFERENCES "#__anodos_currency"("id"),
	"rate_date" date NOT NULL,
	"quantity" int8 NOT NULL DEFAULT '1',
	"rate" DECIMAL(12,4) NOT NULL,
	"state" int NOT NULL DEFAULT '1',
	"created" timestamp NOT NULL DEFAULT '1980.001',
	"created_by" int8 NOT NULL DEFAULT '0',
	"modified" timestamp NOT NULL DEFAULT '1980.001',
	"modified_by" int8 NOT NULL DEFAULT '0',
	"publish_up" timestamp NOT NULL DEFAULT '1980.001',
	"publish_down" timestamp NOT NULL DEFAULT '2100.001',
	PRIMARY KEY ("rate_date", "currency_id")
);
  
INSERT INTO "#__anodos_currency_rate" (
	"currency_id",
	"rate_date",
	"quantity",
	"rate",
	"state")
VALUES
	('1', '1980.001', '1', '1', '1');
