CREATE TABLE `users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `reminddate` date NULL,
  PRIMARY KEY (`email`)
);

CREATE TABLE `tokens` (
  `email` varchar(50) NOT NULL,
  `token` varchar(25) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY (`token`)
);

CREATE TABLE `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`owner`),
  FOREIGN KEY (`owner`) REFERENCES users(email)
);

CREATE TABLE `debtors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `owner` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`owner`),
  KEY (`email`),
  FOREIGN KEY (`owner`) REFERENCES users(email)
);

CREATE TABLE `credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `debtor` int NOT NULL,
  `comment` varchar(250) NOT NULL,
  `amount` int NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`debtor`),
  FOREIGN KEY (`debtor`) REFERENCES debtors(id)
);


CREATE TABLE `debts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity` int NOT NULL,
  `debtor` int NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `amount` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`debtor`),
  KEY (`activity`),
  FOREIGN KEY (`debtor`) REFERENCES debtors(id),
  FOREIGN KEY (`activity`) REFERENCES activities(id) ON DELETE CASCADE
);


CREATE TABLE `recurring` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `amount` int NOT NULL,
  `start` date NOT NULL,
  `frequency` varchar(5) NOT NULL,
  `lastrun` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`owner`),
  KEY (`lastrun`),
  FOREIGN KEY (`owner`) REFERENCES users(email)
);

CREATE TABLE `recurring_debtors` (
  `recurringid` int NOT NULL,
  `debtor` int NOT NULL,
  PRIMARY KEY (`recurringid`, `debtor`),
  FOREIGN KEY (`recurringid`) REFERENCES recurring(id)  ON DELETE CASCADE,
  FOREIGN KEY (`debtor`) REFERENCES debtors(id)
);


CREATE TABLE `pending_users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `confirmation` varchar(25) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY (`confirmation`)
);

CREATE TABLE `aliases` (
  `email` varchar(50) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `unconfirmed` varchar(25) NULL,
  KEY (`owner`),
  FOREIGN KEY (`owner`) REFERENCES users(email),
  UNIQUE KEY (`unconfirmed`)
);

CREATE TABLE `config` (
  `id` varchar(50) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`id`)
);
INSERT INTO `config` VALUES ('schema', '3');
INSERT INTO `config` VALUES ('cron', '0');
