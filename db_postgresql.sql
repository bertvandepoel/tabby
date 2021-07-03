CREATE TABLE "users" (
  "email" varchar(50) NOT NULL,
  "name" varchar(50) NOT NULL,
  "password" varchar(255) NOT NULL,
  "iban" varchar(34) NOT NULL,
  "reminddate" date NULL,
  PRIMARY KEY ("email")
);


CREATE TABLE "tokens" (
  "email" varchar(50) NOT NULL,
  "token" varchar(25) NOT NULL,
  PRIMARY KEY ("email"),
  UNIQUE ("token")
);


CREATE TABLE "activities" (
  "id" serial,
  "name" varchar(250) NOT NULL,
  "owner" varchar(50) NOT NULL,
  "date" date NOT NULL,
  PRIMARY KEY ("id"),
  FOREIGN KEY ("owner") REFERENCES users(email)
);
CREATE INDEX ON "activities" ("owner");


CREATE TABLE "debtors" (
  "id" serial,
  "name" varchar(50) NOT NULL,
  "email" varchar(50) NOT NULL,
  "owner" varchar(50) NOT NULL,
  PRIMARY KEY ("id"),
  FOREIGN KEY ("owner") REFERENCES users(email)
);
CREATE INDEX ON "debtors" ("owner");
CREATE INDEX ON "debtors" ("email");


CREATE TABLE "credits" (
  "id" serial,
  "debtor" integer NOT NULL,
  "comment" varchar(250) NOT NULL,
  "amount" integer NOT NULL,
  "date" date NOT NULL,
  PRIMARY KEY ("id"),
  FOREIGN KEY ("debtor") REFERENCES debtors(id)
);
CREATE INDEX ON "credits" ("debtor");


CREATE TABLE "debts" (
  "id" serial,
  "activity" integer NOT NULL,
  "debtor" integer NOT NULL,
  "comment" varchar(250) DEFAULT NULL,
  "amount" integer NOT NULL,
  PRIMARY KEY ("id"),
  FOREIGN KEY ("debtor") REFERENCES debtors(id),
  FOREIGN KEY ("activity") REFERENCES activities(id) ON DELETE CASCADE
);
CREATE INDEX ON "debts" ("debtor");
CREATE INDEX ON "debts" ("activity");


CREATE TABLE "recurring" (
  "id" serial,
  "name" varchar(250) NOT NULL,
  "owner" varchar(50) NOT NULL,
  "amount" integer NOT NULL,
  "start" date NOT NULL,
  "frequency" varchar(5) NOT NULL,
  "lastrun" date DEFAULT NULL,
  PRIMARY KEY ("id"),
  FOREIGN KEY ("owner") REFERENCES users(email)
);
CREATE INDEX ON "recurring" ("owner");
CREATE INDEX ON "recurring" ("lastrun");

CREATE TABLE "recurring_debtors" (
  "recurringid" integer NOT NULL,
  "debtor" integer NOT NULL,
  PRIMARY KEY ("recurringid", "debtor"),
  FOREIGN KEY ("recurringid") REFERENCES recurring(id)  ON DELETE CASCADE,
  FOREIGN KEY ("debtor") REFERENCES debtors(id)
);


CREATE TABLE "pending_users" (
  "email" varchar(50) NOT NULL,
  "name" varchar(50) NOT NULL,
  "password" varchar(255) NOT NULL,
  "iban" varchar(34) NOT NULL,
  "confirmation" varchar(25) NOT NULL,
  "datetime" timestamp NOT NULL,
  PRIMARY KEY ("email"),
  UNIQUE ("confirmation")
);

CREATE TABLE "aliases" (
  "email" varchar(50) NOT NULL,
  "owner" varchar(50) NOT NULL,
  "unconfirmed" varchar(25) NULL,
  FOREIGN KEY ("owner") REFERENCES users(email),
  UNIQUE ("unconfirmed")
);
CREATE INDEX ON "aliases" ("owner");

CREATE TABLE "config" (
  "id" varchar(50) NOT NULL,
  "value" TEXT NOT NULL,
  PRIMARY KEY ("id")
);
INSERT INTO "config" VALUES ('schema', '3');
INSERT INTO "config" VALUES ('cron', '0');
