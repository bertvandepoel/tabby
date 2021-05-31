<?php

function add_activity($name, $date) {
	global $db;
	$insert = $db->prepare('INSERT INTO activities (name, owner, date) VALUES (?,?,?)');
	$insert->execute(array($name, $_SESSION['tabby_loggedin'], $date));
	return $db->lastInsertId();
}

function add_debt($actid, $debtor, $comment, $amount) {
	global $db;
	$insert = $db->prepare('INSERT INTO debts (activity, debtor, comment, amount) VALUES (?,?,?,?)');
	$insert->execute(array($actid, $debtor, $comment, $amount));
}

function email_new_debt($debtor, $user, $actname, $actdate, $comment, $amount, $total, $token) {
	global $base_url;
	global $application_email;
	
	if($total <= -$amount) { // No credit or outstanding debt
		$message = "Hi there,\r\n\r\nThis is a notification that " . $user['name'] . " has added \"" . $actname . "\" in Tabby as a new activity that took place on " . $actdate . ". You owe them " . human_friendly_amount($amount, FALSE, TRUE) . " for this activity, they mentioned the following details for you: \"" . $comment . "\". This brings your total debt to " . human_friendly_amount(-$total, FALSE, TRUE) . ". You can transfer the money to the following bank account: " . $user['iban'] . "\r\n\r\nYou can see an overview of all of your debt by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	elseif($total > 0) { // More than enough credit to cover the cost
		$message = "Hi there,\r\n\r\nThis is a notification that " . $user['name'] . " has added \"" . $actname . "\" in Tabby as a new activity that took place on " . $actdate . ". Your cost is " . human_friendly_amount($amount, FALSE, TRUE) . " for this activity, they mentioned the following details for you: \"" . $comment . "\". Your account had enough outstanding credit to offset this cost. You currently have " . human_friendly_amount($total, FALSE, TRUE) . " of credit left.\r\n\r\nYou can see an overview of all costs and credits by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	elseif($total == 0) { // Just enough credit to cover the debt
		$message = "Hi there,\r\n\r\nThis is a notification that " . $user['name'] . " has added \"" . $actname . "\" in Tabby as a new activity that took place on " . $actdate . ". Your cost is " . human_friendly_amount($amount, FALSE, TRUE) . " for this activity, they mentioned the following details for you: \"" . $comment . "\". Your account had just enough outstanding credit to offset this cost, so you now have " . human_friendly_amount(0, FALSE, TRUE) . " of credit left.\r\n\r\nYou can see an overview of all costs and credits by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	else { // Some credit, but not enough to fully pay the debt
		$message = "Hi there,\r\n\r\nThis is a notification that " . $user['name'] . " has added \"" . $actname . "\" in Tabby as a new activity that took place on " . $actdate . ". Your cost is " . human_friendly_amount($amount, FALSE, TRUE) . " for this activity, they mentioned the following details for you: \"" . $comment . "\". Your account had some outstanding credit but not enough to fully offset the cost. This leaves a remaining debt of " . human_friendly_amount(-$total, FALSE, TRUE) . ". You can transfer the money to the following bank account: " . $user['iban'] . "\r\n\r\nYou can see an overview of all costs and credits by visiting " . $base_url . "token/" . $token . "\r\n\r\nHave a nice day!\r\n\r\nTabby";
	}
	
	$headers = array(
		'From' => $application_email,
		'Reply-To' => $user['email'],
		'Content-Type' => 'text/plain; charset=UTF-8'
	);
	mail($debtor['email'], 'Tabby: ' . $user['name'] . ' added ' . $actname, $message, $headers);
}

function del_activity($id) {
	global $db;
	$del = $db->prepare('DELETE FROM activities WHERE id=? AND owner=?');
	$del->execute(array($id, $_SESSION['tabby_loggedin']));
	if($del->rowCount() > 0) {
		return TRUE;
	}
	return FALSE;
}

function get_activity($actid) {
	global $db;
	$get = $db->prepare('SELECT id, name, date FROM activities WHERE id=? AND owner=?');
	$get->execute(array($actid, $_SESSION['tabby_loggedin']));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	if(!$result) {
		return FALSE;
	}
	return $result;
}
