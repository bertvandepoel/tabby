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

CREATE TABLE "config" (
  "id" varchar(50) NOT NULL,
  "value" TEXT NOT NULL,
  PRIMARY KEY ("id")
);
INSERT INTO "config" VALUES ('schema', '2');
INSERT INTO "config" VALUES ('cron', '0');
