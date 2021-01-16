<?php

try {
	if(is_int(strpos($dsn, 'mysql'))) {
		$db = new PDO($dsn, $db_username, $db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	}
	else {
		$db = new PDO($dsn, $db_username, $db_password);
	}
} catch (PDOException $e) {
	$error = 'Something went wrong while connecting to the database.';
	include('templates/error.php');
	include('templates/footer.php');
	exit;
}

ini_set('display_errors', '0');
session_start();