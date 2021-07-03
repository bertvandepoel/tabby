<?php

header('Content-Type: text/plain');

if(is_int(strpos(file_get_contents('config.php'), 'PDOException'))) {
	echo "If you're upgrading from database schema 1 (any version from Tabby that came without an installer or upgrader), you will also need to perform some manual changes. To automate installation and upgrades, the configuration file has been reviewed to no longer include the PDO connection string. Abstractions have also been made to better support links and to add PostgreSQL support.\n\n";
	echo "Specifically change the following inside the config.php file:\n";
	echo "1. Remove everything from the line starting with ini_set to the end of the file\n";
	echo "2. Add a \$dsn variable above \$application_email and have it contain the first part of the contents of 'new PDO', for example 'mysql:host=localhost;dbname=tabby'. Don't forget to always end with a ';'. The resulting line should be similar to: \$dsn = 'mysql:host=localhost;dbname=tabby';\n";
	echo "3. Underneath the \$dsn, add a line for the database username with \$db_username, do the same for the password with \$db_password (the contents are after the DSN in new PDO)\n";
	echo "4. Change the value of \$base_url to the value of \$reminderurl\n";
	echo "5. Remove the line starting with \$reminderurl\n";
	echo "6. Add the following on a new line after \$days: \$webcron = false;\n";
	echo "7. Remove the 6 database connection lines from the line starting with try, over the line with catch to the final closing curly bracket\n\n";
	echo "After performing these steps, your configuration file should look similar to this but with your personal credentials:\n";
	echo "<?php

\$dsn = 'mysql:host=localhost;dbname=tabby';
\$db_username = 'tabby';
\$db_password = 'correct horse batter staple';

\$application_email = 'no-reply@localhost';
\$admin_email = 'admin@localhost';
\$base_url = 'http://localhost/tabby/';
\$days = 5;
\$webcron = false;\n\n";
	echo "After performing these changes to the configuration, you should be able to rerun or reload this upgrade script to perform the necessary changes to the database schema.\n\n";
	exit;
}

if(!is_int(strpos(file_get_contents('config.php'), 'currency'))) {
	echo "It seems your current Tabby configuration doesn't specify a currency. While originally Tabby just supported Euro, more currencies have been added now (available through the installer).\n\nPlease edit your config.php file and add a \$currency option at the end of the file, for example:\n\n";
	echo "\$currency = '€';";
}


include('config.php');
include('resources/init.php');

$get_schema = $db->prepare('SELECT value FROM config WHERE id=?');
$get_schema->execute(array('schema'));
$result = $get_schema->fetch(PDO::FETCH_ASSOC);
$schema = $result['value'];
if(intval($schema) < 1) {
	$schema = 1;
}

switch ($schema) {
	case 1:
		echo "Upgrading database schema version 1 to version 2\n";
		$db->query('ALTER TABLE activities CHANGE user owner VARCHAR(50);');
		echo '.';
		$db->query('ALTER TABLE activities MODIFY COLUMN id int;');
		echo '.';
		$db->query('ALTER TABLE debtors CHANGE user owner VARCHAR(50);');
		echo '.';
		$db->query('ALTER TABLE debtors MODIFY COLUMN id int;');
		echo '.';
		$db->query('ALTER TABLE credits MODIFY COLUMN id int;');
		echo '.';
		$db->query('ALTER TABLE credits MODIFY COLUMN debtor int;');
		echo '.';
		$db->query('ALTER TABLE credits MODIFY COLUMN amount int;');
		echo '.';
		$db->query('ALTER TABLE debts MODIFY COLUMN id int;');
		echo '.';
		$db->query('ALTER TABLE debts MODIFY COLUMN activity int;');
		echo '.';
		$db->query('ALTER TABLE debts MODIFY COLUMN debtor int;');
		echo '.';
		$db->query('ALTER TABLE debts MODIFY COLUMN amount int;');
		echo '.';
		$db->query('CREATE TABLE `config` ( `id` varchar(50) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`) );');
		echo '.';
		$db->query('INSERT INTO `config` VALUES (\'schema\', \'2\');');
		echo '.';
		$db->query('INSERT INTO `config` VALUES (\'cron\', \'0\');');
		echo "\nAll queries have been executed. Database is now on schema version 2\n\n";
	case 2:
		echo "Upgrading database schema version 2 to version 3\n";
		if(strpos($dsn, 'mysql:') === 0) {
			$db->query('CREATE TABLE `recurring` ( `id` int NOT NULL AUTO_INCREMENT, `name` varchar(250) NOT NULL, `owner` varchar(50) NOT NULL, `amount` int NOT NULL, `start` date NOT NULL, `frequency` varchar(5) NOT NULL, `lastrun` date DEFAULT NULL, PRIMARY KEY (`id`), KEY (`owner`), KEY (`lastrun`), FOREIGN KEY (`owner`) REFERENCES users(email) );');
			echo '.';
			$db->query('CREATE TABLE `recurring_debtors` ( `recurringid` int NOT NULL, `debtor` int NOT NULL, PRIMARY KEY (`recurringid`, `debtor`), FOREIGN KEY (`recurringid`) REFERENCES recurring(id)  ON DELETE CASCADE, FOREIGN KEY (`debtor`) REFERENCES debtors(id) );');
			echo '.';
			$db->query('CREATE TABLE `aliases` ( `email` varchar(50) NOT NULL, `owner` varchar(50) NOT NULL, `unconfirmed` varchar(25) NULL, KEY (`owner`), FOREIGN KEY (`owner`) REFERENCES users(email), UNIQUE KEY (`unconfirmed`) );');
			echo '.';
		}
		else {
			$db->query('CREATE TABLE "recurring" ( "id" serial, "name" varchar(250) NOT NULL, "owner" varchar(50) NOT NULL, "amount" integer NOT NULL, "start" date NOT NULL, "frequency" varchar(5) NOT NULL, "lastrun" date DEFAULT NULL, PRIMARY KEY ("id"), FOREIGN KEY ("owner") REFERENCES users(email) );');
			echo '.';
			$db->query('CREATE INDEX ON "recurring" ("owner");');
			echo '.';
			$db->query('CREATE INDEX ON "recurring" ("lastrun");');
			echo '.';
			$db->query('CREATE TABLE "recurring_debtors" ( "recurringid" integer NOT NULL, "debtor" integer NOT NULL, PRIMARY KEY ("recurringid", "debtor"), FOREIGN KEY ("recurringid") REFERENCES recurring(id)  ON DELETE CASCADE, FOREIGN KEY ("debtor") REFERENCES debtors(id) );');
			echo '.';
			$db->query('CREATE TABLE "aliases" ( "email" varchar(50) NOT NULL, "owner" varchar(50) NOT NULL, "unconfirmed" varchar(25) NULL, FOREIGN KEY ("owner") REFERENCES users(email), UNIQUE ("unconfirmed") );');
			echo '.';
			$db->query('CREATE INDEX ON "aliases" ("owner");');
			echo '.';
		}
		$db->query('UPDATE config SET value=\'3\' WHERE id=\'schema\';');
		echo '.';
		echo "\nAll queries have been executed. Database is now on schema version 3\n\n";
}
