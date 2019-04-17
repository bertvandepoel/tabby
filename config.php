<?php

try {
	$db = new PDO('mysql:host=localhost;dbname=tabby', 'user', 'password', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
} catch (PDOException $e) {
	echo 'Something\'s wrong';
	die();
}

$application_email = 'no-reply@example.com';
$admin_email = 'admin@example.com';
$base_url = '/tabby/'; //end with a slash, just put a slash if there's no subfolders involved
$days = 5; //how many days before a user is reminded to check his bank account and remind any remaining debtors
$reminderurl = 'https://example.com/tabby/'; //reminder does not have access to SERVER_NAME and therefore requires a separate URL

ini_set('display_errors', '0');
session_start();

?>
