<?php

include('config.php');
include('resources/users.php');
include('resources/transactions.php');

$users = get_users_by_reminddif($days);

foreach($users as $user) {
	if(user_have_debtors_in_debt($user['email'])) {
		if(is_null($user['reminddate'])) {
			$message = "Hi " . $user['name'] . ",\r\n\r\nIt seems you've never sent any reminders on Tabby. Since some people still have an open debt with you, it's probably best if you check your bank account, update any information on Tabby and then sent out new reminders if required.\r\n\r\nYou can get started straight away at " . $reminderurl . ".\r\n\r\nTabby will remind you every " . $days . " days as long as there is open debt.\r\n\r\nHave a nice day!\r\n\r\nTabby";
		}
		else {
			$message = "Hi " . $user['name'] . ",\r\n\r\nIt seems you haven't sent any new reminders on Tabby since " . date('d M Y', strtotime($user['reminddate'])) . ". Since some people still have an open debt with you, it's probably best if you check your bank account, update any information on Tabby and then sent out new reminders if required.\r\n\r\nYou can get started straight away at " . $reminderurl . ".\r\n\r\nTabby will remind you every " . $days . " days as long as there is open debt.\r\n\r\nHave a nice day!\r\n\r\nTabby";
		}
		
		$headers = 'From: ' . $application_email;
		mail($user['email'], 'Tabby: time to send reminders', $message, $headers);
	}
}
