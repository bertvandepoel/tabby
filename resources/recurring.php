<?php

function get_user_recurring() {
	global $db;
	$get = $db->prepare('SELECT id, name, amount, start, frequency, lastrun FROM recurring WHERE owner=? ORDER BY name');
	$get->execute(array($_SESSION['tabby_loggedin']));
	$result = array();
	while($row = $get->fetch(PDO::FETCH_ASSOC)) {
		$row['debtors'] = _get_recurring_debtors($row['id'], $_SESSION['tabby_loggedin']);
		$result[] = $row;
	}
	return $result;
}

function get_all_recurring() {
	global $db;
	$get = $db->prepare('SELECT id, name, owner, amount, start, frequency, lastrun FROM recurring');
	$get->execute(array());
	$result = array();
	while($row = $get->fetch(PDO::FETCH_ASSOC)) {
		$row['debtors'] = _get_recurring_debtors($row['id'], $row['owner']);
		$result[] = $row;
	}
	return $result;
}

function get_recurring($id) {
	global $db;
	$get = $db->prepare('SELECT id, name, amount, start, frequency, lastrun FROM recurring WHERE id=? AND owner=? ORDER BY name');
	$get->execute(array($id, $_SESSION['tabby_loggedin']));
	$result = $get->fetch(PDO::FETCH_ASSOC);
	$result['debtors'] = _get_recurring_debtors($result['id'], $result['owner'], $_SESSION['tabby_loggedin']);
	return $result;
}

function _get_recurring_debtors($recurringid, $owner) {
	global $db;
	$get = $db->prepare('SELECT d.id AS id, d.name AS name FROM recurring_debtors as r, debtors as d WHERE r.recurringid=? AND owner=? AND r.debtor=d.id ORDER BY name');
	$get->execute(array($recurringid, $owner));
	$results = $get->fetchAll(PDO::FETCH_ASSOC);
	$return['id'] = array_column($results, 'id');
	$return['name'] = array_column($results, 'name'); 
	return $return;
}

function add_recurring($name, $amount, $start, $frequency, $debtors) {
	global $db;
	$insert = $db->prepare('INSERT INTO recurring (name, owner, amount, start, frequency) VALUES (?,?,?,?,?)');
	$insert->execute(array($name, $_SESSION['tabby_loggedin'], $amount, $start, $frequency));
	$recurringid = $db->lastInsertId();
	$insert = $db->prepare('INSERT INTO recurring_debtors VALUES (?,?)');
	foreach($debtors as $debtor) {
		$insert->execute(array($recurringid, $debtor));
	}
	return $recurringid;
}

function update_recurring($id, $name, $amount, $debtors) {
	global $db;
	$update = $db->prepare('UPDATE recurring SET name=?, amount=? WHERE id=? AND owner=?');
	$update->execute(array($name, $amount, $id, $_SESSION['tabby_loggedin']));
	if($update->rowCount() === 1) {
		$delete = $db->prepare('DELETE FROM recurring_debtors WHERE recurringid=?');
		$delete->execute(array($id));
		$insert = $db->prepare('INSERT INTO recurring_debtors VALUES (?,?)');
		foreach($debtors as $debtor) {
			$insert->execute(array($id, $debtor));
		}
	}
}

function del_recurring($id) {
	global $db;
	$del = $db->prepare('DELETE FROM recurring WHERE id=? AND owner=?');
	$del->execute(array($id, $_SESSION['tabby_loggedin']));
	if($del->rowCount() > 0) {
		return TRUE;
	}
	return FALSE;
}

function execute_recurring($id) {
	global $db;
	$get = $db->prepare('SELECT name, owner, amount FROM recurring WHERE id=?');
	$get->execute(array($id));
	$recurring = $get->fetch(PDO::FETCH_ASSOC);
	$actid = add_activity('Recurring expense', date('Y-m-d'), $recurring['owner']);
	
	$get = $db->prepare('SELECT d.id AS id, d.name AS name, d.email AS email FROM recurring_debtors as r, debtors as d WHERE r.recurringid=? AND r.debtor=d.id ORDER BY name');
	$get->execute(array($id));
	while($debtor = $get->fetch(PDO::FETCH_ASSOC)) {
		add_debt($actid, $debtor['id'], $recurring['name'], $recurring['amount']);
		$user = get_user_details($recurring['owner']);
		$finstate = get_debtor_financial_state($debtor['email'], $recurring['owner']);
		$token = get_debtor_token($debtor['email']);
		if($token === FALSE) {
			$token = create_debtor_token($debtor['email']);
		}
		email_new_debt($debtor, $user, 'Recurring expense', date('d M Y'), $recurring['name'], $recurring['amount'], $finstate['total'], $token);
	}

	$update = $db->prepare('UPDATE recurring SET lastrun=? WHERE id=?');
	$update->execute(array(date('Y-m-d'), $id));
}

function frequency_to_dateintervalstring($frequency, $days = 0) {
	if($frequency == 'yearly') {
		return 'P1Y';
	}
	elseif($frequency == 'monthly') {
		return 'P1M';
	}
	elseif($frequency == 'weekly') {
		return 'P1W';
	}
	elseif($frequency == 'days' AND $days > 0) {
		return 'P' . $days . 'D';
	}
	else {
		return FALSE;
	}
}

function dateintervalstring_to_frequency($interval) {
	if($interval == 'P1Y') {
		return 'yearly';
	}
	elseif($interval == 'P1M') {
		return 'monthly';
	}
	elseif($interval == 'P1W' OR $interval == 'P7D') {
		return 'weekly';
	}
	else {
		return substr($interval, 1, -1) . ' days';
	}
}

function get_nextrun($start, $frequency, $lastrun) {
	if(is_null($lastrun)) {
		return $start;
	}
	$nextrun = new DateTime($lastrun);
	$nextrun->add(new DateInterval($frequency));
	return $nextrun->format('Y-m-d');
}
