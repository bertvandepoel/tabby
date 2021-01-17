<?php

function get_debtors() {
	global $db;
	$get = $db->prepare('SELECT name, email FROM debtors WHERE owner=? ORDER BY name');
	$get->execute(array($_SESSION['tabby_loggedin']));
	return $get->fetchAll(PDO::FETCH_ASSOC);
}

function check_debtor($email) {
	global $db;
	$get = $db->prepare('SELECT count(*) FROM debtors WHERE email=? AND owner=?');
	$get->execute(array($email, $_SESSION['tabby_loggedin']));
	if($get->fetchColumn() == 0) {
		return TRUE;
	}
	return FALSE;
}

function add_debtor($name, $email) {
	global $db;
	$insert = $db->prepare('INSERT INTO debtors (name, email, owner) VALUES (?,?,?)');
	$insert->execute(array($name, $email, $_SESSION['tabby_loggedin']));
}

function update_debtor($oldemail, $newname, $newemail) {
	global $db;
	$update = $db->prepare('UPDATE debtors SET email=?, name=? WHERE email=? AND owner=?');
	$update->execute(array($newemail, $newname, $oldemail, $_SESSION['tabby_loggedin']));
}

function delete_debtor($email) {
	global $db;
	$delete = $db->prepare('DELETE FROM debtors WHERE email=? AND owner=?');
	$delete->execute(array($email, $_SESSION['tabby_loggedin']));
	if($delete->errorCode() === '00000') {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function change_debtor_email($debtor, $email) {
	global $db;
	$update = $db->prepare('UPDATE debtors SET email=? WHERE email=? AND owner=?');
	$update->execute(array($email, $debtor, $_SESSION['tabby_loggedin']));
}

function get_debtor_details($email, $owner = NULL) {
	global $db;
	if(is_null($owner)) {
		$owner = $_SESSION['tabby_loggedin'];
	}
	$get = $db->prepare('SELECT * FROM debtors WHERE email=? AND owner=?');
	$get->execute(array($email, $owner));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	return $result;
}

function get_debtor_token($email) {
	global $db;
	$get = $db->prepare('SELECT token FROM tokens WHERE email=?');
	$get->execute(array($email));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	if(!$result) {
		return FALSE;
	}
	return $result['token'];
}

function create_debtor_token($email) {
	global $db;
	$insert = $db->prepare('INSERT INTO tokens VALUES (?,?)');
	$token = str_rand(25);
	$insert->execute(array($email, $token));
	return $token;
}

function get_token_email($token) {
	global $db;
	$get = $db->prepare('SELECT email FROM tokens WHERE token=?');
	$get->execute(array($token));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	if(empty($result)) {
		return FALSE;
	}
	return $result['email'];
}

function reset_token($token) {
	global $db;
	$update = $db->prepare('UPDATE tokens SET token=? WHERE token=?');
	$newtoken = str_rand(25);
	$update->execute(array($newtoken, $token));
	return $newtoken;
}

function mail_token($email, $token) {
	global $base_url;
	global $application_email;
	$message = "Hi there,\r\n\r\nYou requested an overview of your debt and credit. You can find that on " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	$headers = 'From: ' . $application_email;
	mail($email, 'Tabby: you requested an overview', $message, $headers);
}

function email_reminder($email, $total, $comment, $token, $user) {
	global $base_url;
	global $application_email;
	
	update_reminddate();
	
	if(is_null($comment)) {
		$message = "Hi there,\r\n\r\nThis is a reminder from " . $user['name'] . ". You owe them " . number_format((-$total / 100), 2) . " euro.\r\n\r\nYou can transfer the money to their bank account: " . $user['iban'] . "\r\n\r\nYou can see an overview of all of your debt by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	else {
		$message = "Hi there,\r\n\r\nThis is a reminder from " . $user['name'] . ". You owe them " . number_format((-$total / 100), 2) . " euro.\r\n\r\nThey added the following message for you \"" . $comment . "\".\r\n\r\nYou can transfer the money to their bank account: " . $user['iban'] . "\r\n\r\nYou can see an overview of all of your debt by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	
	$headers = array(
		'From' => $application_email,
		'Reply-To' => $user['email']
	);
	mail($email, 'Tabby: ' . $user['name'] . ' is reminding you', $message, $headers);
}
