CREATE TABLE `users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `reminddate` date NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tokens` (
  `email` varchar(50) NOT NULL,
  `token` varchar(25) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `user` varchar(50) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  FOREIGN KEY (`user`) REFERENCES users(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `debtors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `email` (`email`),
  FOREIGN KEY (`user`) REFERENCES users(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `debtor` int(11) NOT NULL,
  `comment` varchar(250) NOT NULL,
  `amount` int(8) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `debtor` (`debtor`),
  FOREIGN KEY (`debtor`) REFERENCES debtors(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `debts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` int(11) NOT NULL,
  `debtor` int(11) NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `amount` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `debtor` (`debtor`),
  KEY `activity` (`activity`),
  FOREIGN KEY (`debtor`) REFERENCES debtors(id),
  FOREIGN KEY (`activity`) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `pending_users` (
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `confirmation` varchar(25) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `confirmation` (`confirmation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
