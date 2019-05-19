<?php
try {
    $db = new PDO($_SERVER["TABBY_DB_DSN"], $_SERVER["TABBY_DB_USER"], $_SERVER["TABBY_DB_PASSWORD"], array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8; SET SESSION sql_mode=\'ANSI_QUOTES\';',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
} catch (PDOException $e) {
    echo 'Something\'s wrong: ' . $e->getMessage() . "\n";
    throw $e;
}

if (!@$_SERVER["TABBY_APPLICATION_EMAIL"]) {
    $application_email = $_SERVER["TABBY_APPLICATION_EMAIL"];
} else {
    $application_email = "noreply@" . $_SERVER["TABBY_DOMAIN"];
}
if (!@$_SERVER["TABBY_ADMIN_EMAIL"]) {
    $admin_email = "root@" . $_SERVER["TABBY_DOMAIN"];
} else {
    $admin_email = $_SERVER["TABBY_ADMIN_EMAIL"];
}
$base_url = $_SERVER["TABBY_BASE_PATH"]; // end with a slash, just put a slash if there's no subfolders involved
$days = intval($_SERVER["TABBY_REMIND_DAYS"]); // how many days before a user is reminded to check his bank account and remind any remaining debtors
$reminderurl = $_SERVER["TABBY_PROTOCOL"] . '://' . $_SERVER["TABBY_DOMAIN"] . $base_url; // reminder does not have access to SERVER_NAME and therefore requires a separate URL

// ini_set('display_errors', '0');
session_start();

?>
