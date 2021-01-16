<?php

include_once('config.php');
include_once('resources/init.php');
include_once('resources/users.php');
include_once('resources/transactions.php');

$check = $db->prepare('SELECT value FROM config WHERE id=?');
$check->execute(array('cron'));
$result = $check->fetch(PDO::FETCH_ASSOC);

if(date('Y-m-d') !== date('Y-m-d', $result['value'])){
	$users = get_users_by_reminddif($days);
	
	foreach($users as $user) {
		if(user_have_debtors_in_debt($user['email'])) {
			if(is_null($user['reminddate'])) {
				$message = "Hi " . $user['name'] . ",\r\n\r\nIt seems you've never sent any reminders on Tabby. Since some people still have an open debt with you, it's probably best if you check your bank account, update any information on Tabby and then sent out new reminders if required.\r\n\r\nYou can get started straight away at " . $base_url . ".\r\n\r\nTabby will remind you every " . $days . " days as long as there is open debt.\r\n\r\nHave a nice day!\r\n\r\nTabby";
			}
			else {
				$message = "Hi " . $user['name'] . ",\r\n\r\nIt seems you haven't sent any new reminders on Tabby since " . date('d M Y', strtotime($user['reminddate'])) . ". Since some people still have an open debt with you, it's probably best if you check your bank account, update any information on Tabby and then sent out new reminders if required.\r\n\r\nYou can get started straight away at " . $base_url . ".\r\n\r\nTabby will remind you every " . $days . " days as long as there is open debt.\r\n\r\nHave a nice day!\r\n\r\nTabby";
			}
			
			$headers = 'From: ' . $application_email;
			mail($user['email'], 'Tabby: time to send reminders', $message, $headers);
		}
	}
	
	$update = $db->prepare('UPDATE config SET value=? WHERE id=?');
	$update->execute(array(strtotime('now'), 'cron'));
}
