<?php

function check_credentials($email, $password) {
	global $db;
	$get = $db->prepare('SELECT password FROM users WHERE email=?');
	$get->execute(array($email));
	$row = $get->fetch();
	if(password_verify($password, $row['password'])) {
		if(password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
			change_password($email, $password);
		}
		return TRUE;
	}
	return FALSE;
}

function change_password($email, $newpassword) {
	global $db;
	$update = $db->prepare('UPDATE users SET password=? WHERE email=?');
	$update->execute(array(password_hash($newpassword, PASSWORD_DEFAULT), $email));
}

function update_user($email, $name, $iban) {
	global $db;
	$update = $db->prepare('UPDATE users SET name=?, iban=? WHERE email=?');
	$update->execute(array($name, $iban, $email));
}

function register_user($email, $name, $password, $iban) {
	global $db;
	global $base_url;
	global $application_email;
	
	$insert = $db->prepare('INSERT INTO pending_users VALUES (?,?,?,?,?,NOW())');
	$confirm = str_rand(25);
	$insert->execute(array($email, $name, password_hash($password, PASSWORD_DEFAULT), $iban, $confirm));
	
	$message = "Hi " . $name . ",\r\n\r\nYou have registered an account with a Tabby instance for debt management.\r\nPlease confirm your account by visiting " . $base_url . "confirm/" . $confirm . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	$headers = 'From: ' . $application_email;
	mail($email, 'Tabby: please confirm your email address', $message, $headers);
}

function user_email_confirm($confirmation) {
	global $db;
	global $base_url;
	global $application_email;
	global $admin_email;
	
	$get = $db->prepare('SELECT count(*) FROM pending_users WHERE confirmation=?');
	$get->execute(array($confirmation));
	if($get->fetchColumn() > 0) {
		$newconfirm = str_rand(25);
		$update = $db->prepare('UPDATE pending_users SET confirmation=? WHERE confirmation=?');
		$update->execute(array($newconfirm, $confirmation));
		
		$message = "Hi there admin,\r\n\r\nAn account has been registered and confirmed for a new user.\r\nYou can confirm the account by visiting " . $base_url . "adminconfirm/" . $newconfirm . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
		$headers = 'From: ' . $application_email;
		mail($admin_email, 'Tabby: new confirmed user', $message, $headers);
		
		return TRUE;
	}
}

function user_admin_confirm($confirmation) {
	global $db;
	global $base_url;
	global $application_email;
	$get = $db->prepare('SELECT * FROM pending_users WHERE confirmation=?');
	$get->execute(array($confirmation));
	$pending = $get->fetch(PDO::FETCH_ASSOC);
	if(!empty($pending)) {
		$insert = $db->prepare('INSERT INTO users VALUES (?,?,?,?,?)');
		$insert->execute(array($pending['email'], $pending['name'], $pending['password'], $pending['iban'], NULL));
		$delete = $db->prepare('DELETE FROM pending_users WHERE confirmation=?');
		$delete->execute(array($confirmation));
		
		$message = "Hi " . $pending['name'] . ",\r\n\r\nYou registered a Tabby account on " . date('d M Y', strtotime($pending['datetime'])) . ". The admin of this instance has just confirmed it. So that means you can now get going.\r\n\r\nGo login at " . $base_url . " start tracking debt.\r\n\r\nHave a nice day!\r\n\r\nTabby";
		$headers = 'From: ' . $application_email;
		mail($pending['email'], 'Tabby: the admin has confirmed your account', $message, $headers);
		
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function user_exists($email) {
	global $db;
	$check_user = $db->prepare('SELECT count(*) FROM users WHERE email=?');
	$check_pending = $db->prepare('SELECT count(*) FROM pending_users WHERE email=?');
	$check_user->execute(array($email));
	$check_pending->execute(array($email));
	if($check_user->fetchColumn() > 0 OR $check_pending->fetchColumn() > 0) {
		return TRUE;
	}
	return FALSE;
}

function get_user_details() {
	global $db;
	$get = $db->prepare('SELECT email, name, iban, reminddate FROM users WHERE email=?');
	$get->execute(array($_SESSION['tabby_loggedin']));
	return $get->fetch(PDO::FETCH_ASSOC);
}

function update_reminddate() {
	global $db;
	$update = $db->prepare('UPDATE users SET reminddate=NOW() WHERE email=?');
	$update->execute(array($_SESSION['tabby_loggedin']));
}

function get_pending_user_from_confirmation($confirmation) {
	global $db;
	$get = $db->prepare('SELECT * FROM pending_users WHERE confirmation=?');
	$get->execute(array($confirmation));
	return $get->fetch(PDO::FETCH_ASSOC);
}

function delete_pending_user($confirmation) {
	global $db;
	$delete = $db->prepare('DELETE FROM pending_users WHERE confirmation=?');
	$delete->execute(array($confirmation));
}

function get_users_by_reminddif($days) {
	global $db;
	$get = $db->prepare('SELECT email, name, reminddate FROM users WHERE reminddate IS NULL OR (DATEDIFF(reminddate, NOW()) % ? = 0 AND reminddate < CURDATE())');
	$get->execute(array($days));
	return $get->fetchAll(PDO::FETCH_ASSOC);
}

function str_rand($length) {
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$return = '';
	for ($i = 0; $i < $length; $i++) {
        $return .= $chars[random_int(0, strlen($chars)-1)];
    }
	return $return;
}
