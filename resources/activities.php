<?php

function add_activity($name, $date) {
	global $db;
	$insert = $db->prepare('INSERT INTO activities (name, "user", date) VALUES (?,?,?)');
	$insert->execute(array($name, $_SESSION['tabby_loggedin'], $date));
	return $db->lastInsertId();
}

function add_debt($actid, $debtor, $comment, $amount) {
	global $db;
	$insert = $db->prepare('INSERT INTO debts (activity, debtor, comment, amount) VALUES (?,?,?,?)');
	$insert->execute(array($actid, $debtor, $comment, $amount));
}

function email_new_debt($debtor, $user, $actname, $actdate, $comment, $amount, $token) {
	global $reminderurl;
	global $application_email;

	$message = "Hi there,\r\n\r\nThis is a notification that " . $user['name'] . " has added \"" . $actname . "\" in Tabby as a new activity that took place on " . $actdate . ". You owe them " . number_format(($amount / 100), 2) . " euro for this activity, they mentioned the following details for you \"" . $comment . "\". You can transfer the money to their bank account: " . $user['iban'] . "\r\n\r\nYou can see an overview of all of your debt by visiting " . $reminderurl . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	$headers = array(
		'From' => $application_email,
		'Reply-To' => $user['email']
	);
	mail($debtor['email'], 'Tabby: ' . $user['name'] . ' added ' . $actname, $message, $headers);
}

function del_activity($id) {
	global $db;
	$del = $db->prepare('DELETE FROM activities WHERE id=? AND "user"=?');
	$del->execute(array($id, $_SESSION['tabby_loggedin']));
	if($del->rowCount() > 0) {
		return TRUE;
	}
	return FALSE;
}

function get_activity($actid) {
	global $db;
	$get = $db->prepare('SELECT id, name, date FROM activities WHERE id=? AND "user"=?');
	$get->execute(array($actid, $_SESSION['tabby_loggedin']));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	if(!$result) {
		return FALSE;
	}
	return $result;
}
