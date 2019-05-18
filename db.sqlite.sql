CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `reminddate` date NULL,
  PRIMARY KEY (`email`)
);

CREATE TABLE IF NOT EXISTS `tokens` (
  `email` varchar(50) NOT NULL,
  `token` varchar(25) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE (`token`)
);

CREATE TABLE IF NOT EXISTS `activities` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(250) NOT NULL,
  `user` varchar(50) NOT NULL,
  `date` date NOT NULL,
  FOREIGN KEY (`user`) REFERENCES users(email)
);

CREATE TABLE IF NOT EXISTS `debtors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  FOREIGN KEY (`user`) REFERENCES users(email)
);

CREATE TABLE IF NOT EXISTS `credits` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `debtor` INTEGER NOT NULL,
  `comment` varchar(250) NOT NULL,
  `amount` int(8) NOT NULL,
  `date` date NOT NULL,
  FOREIGN KEY (`debtor`) REFERENCES debtors(id)
);


CREATE TABLE IF NOT EXISTS `debts` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `activity` INTEGER NOT NULL,
  `debtor` INTEGER NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `amount` int(8) NOT NULL,
  FOREIGN KEY (`debtor`) REFERENCES debtors(id),
  FOREIGN KEY (`activity`) REFERENCES activities(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS `pending_users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `confirmation` varchar(25) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE (`confirmation`)
);
