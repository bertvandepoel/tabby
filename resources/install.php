<?php

function create_db($db_type) {
	global $db;
	if($db_type === 'pgsql') {
		$sql = file_get_contents('db_postgresql.sql');
	}
	else {
		$db->query('ALTER DATABASE `' . $_POST['db_name'] . '` DEFAULT CHARSET=utf8mb4;');
		$sql = file_get_contents('db_mysql.sql');
	}
	$result = $db->exec($sql);
	if($result === FALSE) {
		return FALSE;
	}
	return TRUE;
}

function create_first_user($email, $name, $password, $iban) {
	global $db;
	$insert = $db->prepare('INSERT INTO users VALUES (?,?,?,?,?)');
	$insert->execute(array($email, $name, password_hash($password, PASSWORD_DEFAULT), $iban, NULL));
	if($insert->rowCount() === 0) {
		return FALSE;
	}
	return TRUE;
}

function create_config($dsn, $db_username, $db_password, $app_email, $admin_email, $base_url, $days, $cron_type) {
	$config = '<?php

$dsn = "' . addslashes($dsn) . '";
$db_username = "' . addslashes($db_username) . '";
$db_password = "' . addslashes($db_password) . '";

$application_email = "' . addslashes($app_email) . '";
$admin_email = "' . addslashes($admin_email) . '";
$base_url = "' . addslashes($base_url) . '";
$days = ' . intval($days) . ';
';
	if($cron_type === 'webcron') {
		$config .= '$webcron = true;';
	}
	else {
		$config .= '$webcron = false;';
	}
	
	$check = file_put_contents('config.php', $config);
	if(!$check) {
		return $config;
	}
	return 'created';
}

function check_install($base_url) {
	$check_register = is_int(strpos(file_get_contents($base_url . 'register'), 'This installation of Tabby is a private instance.'));
	$check_changelog = is_int(strpos(file_get_contents($base_url . 'changelog.txt'), 'version 1.0 - commit 67b554a08bbed216423b8d968c67ddfe8169df2a'));
	if($check_register AND !$check_changelog) {
		return 'OK';
	}
	if(!$check_register AND $check_changelog) {
		return 'doublefail';
	}
	if(!$check_register) {
		return 'regfail';
	}
	return 'changelogfail';
}
