<?php

function get_transactions_per_activity() {
	global $db;
	$activities = array();
	$get = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.comment AS comment, debts.amount AS amount, debtors.id AS debtorid, debtors.name AS name FROM activities AS a, debts, debtors WHERE a.owner=? AND debtors.owner=? AND a.id=debts.activity AND debts.debtor=debtors.id ORDER BY activity_date DESC, activity_id DESC');
	$get->execute(array($_SESSION['tabby_loggedin'], $_SESSION['tabby_loggedin']));
	$finstate = get_all_debtor_financial_state();
	while($row = $get->fetch(PDO::FETCH_ASSOC)) {
		if(in_array($row['activity_id'], $finstate[$row['debtorid']]['red'])) {
			$row['color'] = 'red';
		}
		elseif(in_array($row['activity_id'], $finstate[$row['debtorid']]['orange'])) {
			$row['color'] = 'orange';
		}
		else {
			$row['color'] = 'neutral';
		}
		if(!isset($activities[$row['activity_id']])) {
			$activities[$row['activity_id']] = array('id' => $row['activity_id'], 'name' => $row['activity_name'], 'date' => $row['activity_date'], 'data' => array());
		}
		$activities[$row['activity_id']]['data'][] = array('name' => $row['name'], 'comment' => $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
	}
	return $activities;
}

function get_activity_transactions($actid) {
	global $db;
	$activity = array();
	$get = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.id AS debtid, debts.comment AS comment, debts.amount AS amount, debtors.id AS debtorid, debtors.name AS name FROM activities AS a, debts, debtors WHERE a.id=? AND a.owner=? AND debtors.owner=? AND a.id=debts.activity AND debts.debtor=debtors.id ORDER BY activity_date DESC, activity_id DESC');
	$get->execute(array($actid, $_SESSION['tabby_loggedin'], $_SESSION['tabby_loggedin']));
	$finstate = get_all_debtor_financial_state();
	while($row = $get->fetch(PDO::FETCH_ASSOC)) {
		if(in_array($row['activity_id'], $finstate[$row['debtorid']]['red'])) {
			$row['color'] = 'red';
		}
		elseif(in_array($row['activity_id'], $finstate[$row['debtorid']]['orange'])) {
			$row['color'] = 'orange';
		}
		else {
			$row['color'] = 'neutral';
		}
		if(empty($activity)) {
			$activity = array('id' => $row['activity_id'], 'name' => $row['activity_name'], 'date' => $row['activity_date'], 'data' => array());
		}
		$activity['data'][] = array('id' => $row['debtid'],'name' => $row['name'], 'comment' => $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
	}
	return $activity;
}

function get_transactions_per_debtor() {
	global $db;
	$debtors = array();
	$get_debt = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.comment AS comment, (debts.amount * -1) AS amount, debtors.id AS debtorid, debtors.name AS name, debtors.email AS email FROM activities AS a, debts, debtors WHERE a.owner=? AND debtors.owner=? AND a.id=debts.activity AND debts.debtor=debtors.id ORDER BY activity_date DESC, activity_id ASC');
	$get_credit = $db->prepare('SELECT debtors.id AS debtorid, credits.amount AS amount, credits.comment AS comment, credits.date AS date, credits.id AS creditid FROM debtors, credits WHERE debtors.owner=? AND debtors.id=credits.debtor ORDER BY date DESC, creditid DESC');
	
	$get_debt->execute(array($_SESSION['tabby_loggedin'], $_SESSION['tabby_loggedin']));
	$get_credit->execute(array($_SESSION['tabby_loggedin']));
	$finstate = get_all_debtor_financial_state();
	
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(in_array($row['activity_id'], $finstate[$row['debtorid']]['red'])) {
			$row['color'] = 'red';
		}
		elseif(in_array($row['activity_id'], $finstate[$row['debtorid']]['orange'])) {
			$row['color'] = 'orange';
		}
		else {
			$row['color'] = 'neutral';
		}
		if(isset($debtors[$row['debtorid']])) {
			if(count($debtors[$row['debtorid']]['data']) == 4) { // we won't display more than 4 records but just give a link for the details
				$debtors[$row['debtorid']]['more'] = TRUE;
				continue;
			}
			$debtors[$row['debtorid']]['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
		}
		else {
			$debtors[$row['debtorid']] = array('name' => $row['name'], 'email' => $row['email'], 'total' => $finstate[$row['debtorid']]['total'], 'more' => FALSE, 'data' => array());
			$debtors[$row['debtorid']]['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
		}
	}
	while($row = $get_credit->fetch(PDO::FETCH_ASSOC)) {
		if(count($debtors[$row['debtorid']]['data']) == 4) { // we won't display more than 4 records but just give a link for the details
			$debtors[$row['debtorid']]['more'] = TRUE;
		}
		$debtors[$row['debtorid']]['data'][] = array('date' => $row['date'], 'sort' => $row['date'] . '-2-' . $row['creditid'], 'description' => $row['comment'], 'amount' => $row['amount'], 'color' => 'green');
	}
	foreach($debtors as $key => $value) {
		usort($debtors[$key]['data'], function ($a, $b) {
			return (-1 * strcmp($a['sort'], $b['sort']));
		});
		$debtors[$key]['data'] = array_reverse(array_slice($debtors[$key]['data'], 0, 4, TRUE), true);
	}
	usort($debtors, function ($a, $b) {
		if($a['total'] < $b['total']) {
			return -1;
		}
		elseif($a['total'] > $b['total']) {
			return 1;
		}
		else {
			// total debt is identical, for example 0
			// if we don't apply further sorting here, it won't make sense to the user
			return ($a['data'][0]['date'] > $b['data'][0]['date']) ? -1 : 1;
		}
	});
	return $debtors;
}

function get_debtor_transactions($debtormail) {
	global $db;
	$debtors = array();
	$get_debt = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.id AS debtid, debts.comment AS comment, (debts.amount * -1) AS amount, debtors.id AS debtorid, debtors.name AS name, debtors.email AS email FROM activities AS a, debts, debtors WHERE a.owner=? AND debtors.owner=? AND debtors.email=? AND a.id=debts.activity AND debts.debtor=debtors.id ORDER BY activity_date DESC, activity_id ASC');
	$get_credit = $db->prepare('SELECT debtors.id AS debtorid, credits.amount AS amount, credits.comment AS comment, credits.date AS date, credits.id AS creditid FROM debtors, credits WHERE debtors.owner=? AND debtors.email=? AND debtors.id=credits.debtor ORDER BY date DESC, creditid DESC');
	
	$get_debt->execute(array($_SESSION['tabby_loggedin'], $_SESSION['tabby_loggedin'], $debtormail));
	$get_credit->execute(array($_SESSION['tabby_loggedin'], $debtormail));
	$finstate = get_debtor_financial_state($debtormail);
	$result = array();
	
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(in_array($row['activity_id'], $finstate['red'])) {
			$row['color'] = 'red';
		}
		elseif(in_array($row['activity_id'], $finstate['orange'])) {
			$row['color'] = 'orange';
		}
		else {
			$row['color'] = 'neutral';
		}
		if(!empty($result)) {
			$result['data'][] = array('id' => 'd' . $row['debtid'], 'date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
		}
		else {
			$result = array('name' => $row['name'], 'email' => $row['email'], 'total' => $finstate['total'], 'more' => FALSE, 'data' => array());
			$result['data'][] = array('id' => 'd' . $row['debtid'], 'date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount'], 'color' => $row['color']);
		}
	}
	while($row = $get_credit->fetch(PDO::FETCH_ASSOC)) {
		$result['data'][] = array('id' => 'c' . $row['creditid'], 'date' => $row['date'], 'sort' => $row['date'] . '-2-' . $row['creditid'], 'description' => $row['comment'], 'amount' => $row['amount'], 'color' => 'green');
	}
	usort($result['data'], function ($a, $b) {
		return (-1 * strcmp($a['sort'], $b['sort']));
	});
	//$result['data'] = array_reverse($result['data'], TRUE);
	return $result;
}

function get_transactions_per_user($debtormail) {
	global $db;
	$users = array();
	$get_debt = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.comment AS comment, (debts.amount * -1) AS amount, debtors.owner AS owner, users.name AS name, users.email AS email, users.iban AS iban FROM activities AS a, debts, debtors, users WHERE debtors.email=? AND a.id=debts.activity AND debts.debtor=debtors.id AND debtors.owner=users.email ORDER BY activity_date DESC, activity_id ASC');
	$get_credit = $db->prepare('SELECT credits.amount AS amount, credits.comment AS comment, credits.date AS date, credits.id AS creditid, debtors.owner AS owner FROM debtors, credits WHERE debtors.email=? AND debtors.id=credits.debtor ORDER BY date DESC, creditid DESC');
	
	$get_debt->execute(array($debtormail));
	$get_credit->execute(array($debtormail));
	
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(isset($users[$row['owner']])) {
			if(count($users[$row['owner']]['data']) == 4) { // we won't display more than 4 records but just give a link for the details
				$users[$row['owner']]['more'] = TRUE;
				$users[$row['owner']]['total'] += $row['amount'];
				continue;
			}
			$users[$row['owner']]['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount']);
			$users[$row['owner']]['total'] += $row['amount'];
		}
		else {
			$users[$row['owner']] = array('user' => $row['owner'], 'name' => $row['name'], 'email' => $row['email'], 'iban' => $row['iban'], 'total' => $row['amount'], 'more' => FALSE, 'data' => array());
			$users[$row['owner']]['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount']);
		}
	}
	while($row = $get_credit->fetch(PDO::FETCH_ASSOC)) {
		if(count($users[$row['owner']]['data']) == 4) { // we won't display more than 4 records but just give a link for the details
			$users[$row['owner']]['more'] = TRUE;
		}
		$users[$row['owner']]['data'][] = array('date' => $row['date'], 'sort' => $row['date'] . '-2-' . $row['creditid'], 'description' => $row['comment'], 'amount' => $row['amount']);
		$users[$row['owner']]['total'] += $row['amount'];
	}
	foreach($users as $key => $value) {
		usort($users[$key]['data'], function ($a, $b) {
			return (-1 * strcmp($a['sort'], $b['sort']));
		});
		$users[$key]['data'] = array_reverse(array_slice($users[$key]['data'], 0, 4, TRUE), true);
	}
	return $users;
}

function get_user_transactions_for_debtor($usermail, $debtormail) {
	global $db;
	$user = array();
	$get_debt = $db->prepare('SELECT a.id AS activity_id, a.name AS activity_name, a.date AS activity_date, debts.comment AS comment, (debts.amount * -1) AS amount, debtors.owner AS owner, users.name AS name, users.email AS email, users.iban AS iban FROM activities AS a, debts, debtors, users WHERE users.email=? AND debtors.email=? AND a.id=debts.activity AND debts.debtor=debtors.id AND debtors.owner=users.email ORDER BY activity_date DESC, activity_id ASC');
	$get_credit = $db->prepare('SELECT credits.amount AS amount, credits.comment AS comment, credits.date AS date, credits.id AS creditid, debtors.owner AS owner FROM debtors, credits WHERE debtors.owner=? AND debtors.email=? AND debtors.id=credits.debtor ORDER BY date DESC, creditid DESC');
	
	$get_debt->execute(array($usermail, $debtormail));
	$get_credit->execute(array($usermail, $debtormail));
	
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(!empty($user)) {
			$user['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount']);
			$user['total'] += $row['amount'];
		}
		else {
			$user = array('user' => $row['owner'], 'name' => $row['name'], 'email' => $row['email'], 'iban' => $row['iban'], 'total' => $row['amount'], 'data' => array());
			$user['data'][] = array('date' => $row['activity_date'], 'sort' => $row['activity_date'] . '-1-' . $row['activity_id'] . $row['comment'], 'description' => $row['activity_name'] . ' - ' . $row['comment'], 'amount' => $row['amount']);
		}
	}
	while($row = $get_credit->fetch(PDO::FETCH_ASSOC)) {
		$user['data'][] = array('date' => $row['date'], 'sort' => $row['date'] . '-2-' . $row['creditid'], 'description' => $row['comment'], 'amount' => $row['amount']);
		$user['total'] += $row['amount'];
	}
	usort($user['data'], function ($a, $b) {
		return (-1 * strcmp($a['sort'], $b['sort']));
	});
	return $user;
}

function get_all_debtor_financial_state() {
	global $db;
	$get_debt = $db->prepare('SELECT debtors.id AS debtorid, debtors.email AS email, SUM(debts.amount) AS debt FROM debtors, debts WHERE debtors.owner=? AND debtors.id=debts.debtor GROUP BY debtorid ORDER BY debtorid');
	$get_credit = $db->prepare('SELECT debtors.id AS debtorid, SUM(credits.amount) AS credit FROM debtors, credits WHERE debtors.owner=? AND debtors.id=credits.debtor GROUP BY debtorid ORDER BY debtorid');
	$get_activities = $db->prepare('SELECT debts.activity AS id, debts.amount AS amount, activities.date AS date FROM debts, activities WHERE debts.debtor=? AND debts.activity=activities.id ORDER BY date DESC');
	
	$get_debt->execute(array($_SESSION['tabby_loggedin']));
	$get_credit->execute(array($_SESSION['tabby_loggedin']));
	$credits = $get_credit->fetchAll(PDO::FETCH_KEY_PAIR);
	$results = array();
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(isset($credits[$row['debtorid']])) {
			$total = $credits[$row['debtorid']] - $row['debt'];
		}
		else {
			$total = - $row['debt'];
		}
		if($total < 0) {
			$get_activities->execute(array($row['debtorid']));
			$missing = -$total;
			$red = array(); //unpaid
			$orange = array(); //partially paid
			while($act = $get_activities->fetch(PDO::FETCH_ASSOC)) {
				if($act['amount'] > $missing) {
					$orange[] = $act['id'];
					break;
				}
				elseif($act['amount'] == $missing) {
					$red[] = $act['id'];
					break;
				}
				else {
					$red[] = $act['id'];
					$missing -= $act['amount'];
				}
			}
			$results[$row['debtorid']] = array('email' => $row['email'], 'total' => $total, 'red' => $red, 'orange' => $orange);
		}
		else {
			$results[$row['debtorid']] = array('email' => $row['email'], 'total' => $total, 'red' => array(), 'orange' => array());
		}
	}
	return $results;
}

function get_debtor_financial_state($debtormail) {
	global $db;
	$get_debt = $db->prepare('SELECT debtors.id AS debtorid, SUM(debts.amount) AS debt FROM debtors, debts WHERE debtors.owner=? AND debtors.email=? AND debtors.id=debts.debtor GROUP BY debtorid ORDER BY debtorid');
	$get_credit = $db->prepare('SELECT debtors.id AS debtorid, SUM(credits.amount) AS credit FROM debtors, credits WHERE debtors.owner=? AND debtors.email=? AND debtors.id=credits.debtor GROUP BY debtorid ORDER BY debtorid');
	$get_activities = $db->prepare('SELECT debts.activity AS id, debts.amount AS amount, activities.date AS date FROM debts, activities WHERE debts.debtor=? AND debts.activity=activities.id ORDER BY date DESC');
	
	$get_debt->execute(array($_SESSION['tabby_loggedin'], $debtormail));
	$get_credit->execute(array($_SESSION['tabby_loggedin'], $debtormail));
	$debt = $get_debt->fetch(PDO::FETCH_ASSOC);
	$credit = $get_credit->fetch(PDO::FETCH_ASSOC);
	$debtorid = $debt['debtorid'];
	$debt = $debt['debt'];
	if(isset($credit['credit'])) {
		$credit = $credit['credit'];
	}
	else {
		$credit = 0;
	}
	$total = $credit - $debt;
	$result = array();
	if($total < 0) {
		$get_activities->execute(array($debtorid));
		$missing = -$total;
		$red = array(); //unpaid
		$orange = array(); //partially paid
		while($act = $get_activities->fetch(PDO::FETCH_ASSOC)) {
			if($act['amount'] > $missing) {
				$orange[] = $act['id'];
				break;
			}
			elseif($act['amount'] == $missing) {
				$red[] = $act['id'];
				break;
			}
			else {
				$red[] = $act['id'];
				$missing -= $act['amount'];
			}
		}
		$result = array('total' => $total, 'red' => $red, 'orange' => $orange);
	}
	else {
		$result = array('total' => $total, 'red' => array(), 'orange' => array());
	}
	return $result;
}

function user_have_debtors_in_debt($usermail) {
	global $db;
	$get_debt = $db->prepare('SELECT debtors.id AS debtorid, SUM(debts.amount) AS debt FROM debtors, debts WHERE debtors.owner=? AND debtors.id=debts.debtor GROUP BY debtorid ORDER BY debtorid');
	$get_credit = $db->prepare('SELECT debtors.id AS debtorid, SUM(credits.amount) AS credit FROM debtors, credits WHERE debtors.owner=? AND debtors.id=credits.debtor GROUP BY debtorid ORDER BY debtorid');
	
	$get_debt->execute(array($usermail));
	$get_credit->execute(array($usermail));
	$credits = $get_credit->fetchAll(PDO::FETCH_KEY_PAIR);
	$results = array();
	while($row = $get_debt->fetch(PDO::FETCH_ASSOC)) {
		if(isset($credits[$row['debtorid']])) {
			$total = $credits[$row['debtorid']] - $row['debt'];
		}
		else {
			$total = - $row['debt'];
		}
		if($total < 0) {
			return TRUE;
		}
	}
	return FALSE;
}

function add_credit($debtor, $comment, $amount, $date) {
	global $db;
	$insert = $db->prepare('INSERT INTO credits (debtor, comment, amount, date) VALUES (?,?,?,?)');
	$insert->execute(array($debtor, $comment, $amount, $date));
}

function del_debt($id) {
	global $db;
	$check = $db->prepare('SELECT count(*) FROM debts, debtors WHERE debts.id=? AND debtors.owner=? AND debtors.id=debts.debtor');
	$check->execute(array($id, $_SESSION['tabby_loggedin']));
	if($check->fetchColumn() > 0) {
		$del = $db->prepare('DELETE FROM debts WHERE id=?');
		$del->execute(array($id));
		return TRUE;
	}
	return FALSE;
}

function del_credit($id) {
	global $db;
	$check = $db->prepare('SELECT count(*) FROM credits, debtors WHERE credits.id=? AND debtors.owner=? AND debtors.id=credits.debtor');
	$check->execute(array($id, $_SESSION['tabby_loggedin']));
	if($check->fetchColumn() > 0) {
		$del = $db->prepare('DELETE FROM credits WHERE id=?');
		$del->execute(array($id));
		return TRUE;
	}
	return FALSE;
}

