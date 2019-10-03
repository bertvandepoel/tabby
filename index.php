<?php

include('config.php');
include('resources/users.php');

$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, $base_url) === 0) {
    $local_uri = substr($uri, strlen($base_url));
}
$local_uri = explode('?', $local_uri);
$location = rtrim($local_uri[0], '/');

include('templates/header.php');

if($location == 'logout') {
	include('templates/emptynav.html');
	unset($_SESSION['tabby_loggedin']);
	$success = 'You\'ve been signed out.';
	include('templates/success.php');
}
elseif(isset($_SESSION['tabby_loggedin'])) {
	include('templates/nav.html');
	include('resources/uiblocks.php');
	include('resources/transactions.php');
	include('resources/people.php');
	include('resources/activities.php');
	if(substr($location, 0, 13) == 'adminconfirm/') {
		$confirmation = explode('adminconfirm/', $location);
		$confirmation = $confirmation[1];
		if(isset($_POST['deny'])) {
			delete_pending_user($confirmation);
			$success = 'This pending user has been deleted.';
			include('templates/success.php');
		}
		elseif(isset($_POST['approve'])) {
			if(user_admin_confirm($confirmation)) {
				$success = 'This user has been contacted his account account has been approved';
				include('templates/success.php');
			}
			else {
				$error = 'Could not approve that user for some reason.';
				include('templates/error.php');
			}
		}
		else {
			$pending = get_pending_user_from_confirmation($confirmation);
			include('templates/adminconfirm.php');
		}
	}
	elseif(substr($location, 0, 6) == 'token/') {
		$success = 'It seems you\'re logged in, redirecting you to your debt overview';
		include('templates/success.php');
	}
	elseif($location == 'activities/add') {
		$debtors = get_debtors();
		if(isset($_POST['add'])) {
			if(strlen($_POST['name']) < 2) {
				$error = 'Your activity needs a name.';
				include('templates/error.php');
				$filled = array('name' => '', 'date' => $_POST['date']);
				include('templates/form_activity.php');
			}
			elseif(!strtotime($_POST['date'])) {
				$error = 'Your activity needs a date.';
				include('templates/error.php');
				$filled = array('name' => $_POST['name'], 'date' => '');
				include('templates/form_activity.php');
			}
			elseif(check_debtor($_POST['debtor'][0]) OR strlen($_POST['comment'][0]) < 2 OR strlen($_POST['amount'][0]) < 1) {
				$error = 'Your activity needs at least one contact with actual debt.';
				include('templates/error.php');
				$filled = array('name' => $_POST['name'], 'date' => $_POST['date']);
				include('templates/form_activity.php');
			}
			else {
				$actid = add_activity($_POST['name'], $_POST['date']);
				for($i=0;;$i++) {
					if(!isset($_POST['comment'][$i]) OR check_debtor($_POST['debtor'][$i]) OR strlen($_POST['comment'][$i]) < 2 OR strlen($_POST['amount'][$i]) < 1) {
						break;
					}
					$debtor = get_debtor_details($_POST['debtor'][$i]);
					$user = get_user_details();
					$amount = str_replace(',', '.', $_POST['amount'][$i]) * 100;
					add_debt($actid, $debtor['id'], $_POST['comment'][$i], $amount);
					if(isset($_POST['sendmail']) AND $_POST['sendmail'] == 1) {
						$token = get_debtor_token($debtor['email']);
						if($token === FALSE) {
							$token = create_debtor_token($debtor['email']);
						}
						email_new_debt($debtor, $user, $_POST['name'], date('d M Y', strtotime($_POST['date'])), $_POST['comment'][$i], $amount, $token);
					}
				}
				$success = 'Your new activity has been added.';
				$redirect = 'people';
				include('templates/success.php');
			}
		}
		elseif($debtors == array()) {
			$error = 'You don\'t have any contacts. You should fix that first before trying to register debt.';
			include('templates/error.php');
		}
		else {
			$filled = array('name' => '', 'date' => '');
			$debtors = get_debtors();
			include('templates/form_activity.php');
		}
	}
	elseif($location == 'people/add') {
		if(isset($_POST['add'])) {
			if(strlen($_POST['name']) < 2) {
				$error = 'Your contact needs a name.';
				include('templates/error.php');
				$filled = array('name' => '', 'email' => $_POST['email']);
				include('templates/form_people.php');
			}
			elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$error = 'That\'s not a real email address!';
				include('templates/error.php');
				$filled = array('name' => $_POST['name'], 'email' => '');
				include('templates/form_people.php');
			}
			elseif(!check_debtor($_POST['email'])) {
				$error = 'Another contact of yours already uses that email address.';
				include('templates/error.php');
				$filled = array('name' => $_POST['name'], 'email' => '');
				include('templates/form_people.php');
			}
			else {
				add_debtor($_POST['name'], $_POST['email']);
				$success = 'Your new contact has been added.';
				$redirect = 'people';
				include('templates/success.php');
			}
		}
		else {
			$filled = array('name' => '', 'email' => '');
			include('templates/form_people.php');
		}
	}
	elseif(substr($location, 0, 18) == 'activities/detail/') {
		if(isset($_GET['del'])) {
			if(del_debt($_GET['del'])) {
				$success = 'Debt deleted.';
				include('templates/success.php');
			}
			else {
				$error = 'Sure you haven\'t deleted that already?';
				include('templates/error.php');
			}
		}
		$actid = explode('activities/detail/', $location);
		$actid = $actid[1];
		title('Detailed view');
		detailcard(get_activity_transactions($actid), 'activities');
	}
	elseif(substr($location, 0, 18) == 'activities/delete/') {
		//confirm before delete, only delete activity because cascade
		if(isset($_POST['delete'])) {
			$actid = explode('activities/delete/', $location);
			$actid = $actid[1];
			if(del_activity($actid)) {
				$success = 'Your activity has been deleted.';
				$redirect = 'activities';
				include('templates/success.php');
			}
			else {
				$error = 'Sure you haven\'t deleted that already?';
				include('templates/error.php');
			}
		}
		else {
			$what = 'activity';
			$warning = 'This will also delete any debt connected to this activity.';
			$backlink = 'activities';
			include('templates/confirm_delete.php');
		}
	}
	elseif(substr($location, 0, 14) == 'people/detail/') {
		if(isset($_GET['del'])) {
			if(substr($_GET['del'], 0, 1) == 'c') {
				if(del_credit(ltrim($_GET['del'], 'c'))) {
					$success = 'Credit deleted.';
					include('templates/success.php');
				}
				else {
					$error = 'Sure you haven\'t deleted that already?';
					include('templates/error.php');
				}
			}
			elseif(substr($_GET['del'], 0, 1) == 'd') {
				if(del_debt(ltrim($_GET['del'], 'd'))) {
					$success = 'Debt deleted.';
					include('templates/success.php');
				}
				else {
					$error = 'Sure you haven\'t deleted that already?';
					include('templates/error.php');
				}
			} 
		}
		$debtormail = explode('people/detail/', $location);
		$debtormail = $debtormail[1];
		title('Detailed view');
		detailcard(get_debtor_transactions($debtormail), 'people');
	}
	elseif(substr($location, 0, 14) == 'mydebt/detail/') {
		$usermail = explode('mydebt/detail/', $location);
		$usermail = $usermail[1];
		title('Detailed view');
		detailcard(get_user_transactions_for_debtor($usermail, $_SESSION['tabby_loggedin']), 'user');
	}
	elseif($location == 'people/list') {
		$debtors = get_debtors();
		include('templates/table_people.php');
	}
	elseif(substr($location, 0, 18) == 'people/list/delete') {
		if(isset($_POST['delete'])) {
			$debtormail = explode('people/list/delete/', $location);
			$debtormail = $debtormail[1];
			if(delete_debtor($debtormail)) {
				$success = 'Your contact has been deleted.';
				$redirect = 'people/list';
				include('templates/success.php');
			}
			else {
				$error = 'This contact can\'t be deleted. Are you sure they have no debt or credit?';
				include('templates/error.php');
			}
		}
		else {
			$what = 'contact';
			$warning = 'This is only possible if your contact has no debt or credit. Make sure to delete that first!';
			$backlink = 'people/list';
			include('templates/confirm_delete.php');
		}
	}
	elseif(substr($location, 0, 16) == 'people/list/edit') {
		$debtormail = explode('people/list/edit/', $location);
		$debtormail = $debtormail[1];
		$debtor = get_debtor_details($debtormail);
		if(isset($_POST['edit'])) {
			if(strlen($_POST['name']) < 2) {
				$error = 'Your contact needs a name.';
				include('templates/error.php');
			}
			elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$error = 'That\'s not a real email address!';
				include('templates/error.php');
			}
			elseif($_POST['edit'] !== $_POST['email'] AND !check_debtor($_POST['email'])) {
				$error = 'Another contact of yours already uses that email address.';
				include('templates/error.php');
			}
			else {
				update_debtor($_POST['edit'], $_POST['name'], $_POST['email']);
				$success = 'Your contact has been updated.';
				$redirect = 'people/list';
				include('templates/success.php');
				$debtor = get_debtor_details($_POST['email']);
			}
		}
		include('templates/form_people_edit.php');
	}
	elseif($location == 'remind') {
		$user = get_user_details();
		if(isset($_POST['submit'])) {
			$finstate = get_all_debtor_financial_state();
			if($_POST['debtor'] == 'TABBY_REMIND_EVERYONE') {
				$error = 'No one has any debt with you.';
				if(strlen($_POST['comment']) > 3) {
					$comment = $_POST['comment'];
				}
				else {
					$comment = NULL;
				}
				foreach($finstate as $debtor) {
					if($debtor['total'] < 0) {
						unset($error);
						$token = get_debtor_token($debtor['email']);
						if($token === FALSE) {
							$token = create_debtor_token($debtor['email']);
						}
						email_reminder($debtor['email'], $debtor['total'], $comment, $token, $user);
					}
				}
				if(isset($error)) {
					include('templates/error.php');
				}
				else {
					$success = 'We\'ve sent your reminder(s).';
					include('templates/success.php');
					$user = get_user_details();
				}
			}
			else {
				$error = 'Weird, I don\'t know about that person.';
				foreach($finstate as $debtor) {
					if($debtor['email'] == $_POST['debtor']) {
						if($debtor['total'] < 0) {
							unset($error);
							$token = get_debtor_token($debtor['email']);
							if($token === FALSE) {
								$token = create_debtor_token($debtor['email']);
							}
							email_reminder($debtor['email'], $debtor['total'], $comment, $token, $user);
							$success = 'We\'ve sent your reminder.';
							include('templates/success.php');
							$user = get_user_details();
							break;
						}
						else {
							$error = 'That person doens\'t have any debt with you right now. No reminder was sent.';
						}
					}
				}
				if(isset($error)) {
					include('templates/error.php');
				}
			}
		}
		$debtors = get_debtors();
		include('templates/form_remind.php');
	}
	elseif($location == 'profile') {
		$user = get_user_details();
		if(isset($_POST['submit'])) {
			if(strlen($_POST['name']) < 5) {
				$error = 'Your name seems oddly short, are you quite sure you didn\'t misspell it?';
			}
			elseif(strlen($_POST['iban']) < 12) {
				$error = 'An IBAN is for sure longer than that. Or do you prefer that people just won\'t pay?';
			}
			elseif(strlen($_POST['password']) < 8) {
				$error = 'Let\'s be realistic, a proper password isn\'t that short.';
			}
			elseif($_POST['name'] === $user['name'] AND $_POST['iban'] === $user['iban'] AND $_POST['password'] === 'TABBY_DEFAULT_VALUE') {
				$error = 'You haven\'t changed anything.';
			}
			else {
				update_user($_SESSION['tabby_loggedin'], $_POST['name'], $_POST['iban']);
				if($_POST['password'] !== 'TABBY_DEFAULT_VALUE') {
					change_password($_SESSION['tabby_loggedin'], $_POST['password']);
				}
				$success = 'Your profile has been updated.';
				include('templates/success.php');
				$user = get_user_details();
			}
			if(isset($error)) {
				include('templates/error.php');
			}
		}
		$filled = array('name' => $user['name'], 'iban' => $user['iban']);
		include('templates/form_profile.php');
	}
	elseif($location == 'mydebt') {
		if(empty(get_transactions_per_user($_SESSION['tabby_loggedin']))) {
			$success = 'No one has any debt for you.';
			include('templates/success.php');
		}
		carddeck(get_transactions_per_user($_SESSION['tabby_loggedin']), 'user');
	}
	elseif($location == 'activities') {
		if(isset($_POST['debt'])) {
			if(check_debtor($_POST['debtor']) OR strlen($_POST['comment']) < 2 OR strlen($_POST['amount']) < 1) {
				$error = 'Please select a contact, fill in a comment and enter an amount to add debt.';
				include('templates/error.php');
			}
			else {
				$debtor = get_debtor_details($_POST['debtor']);
				$amount = str_replace(',', '.', $_POST['amount']) * 100;
				$act = get_activity($_POST['debt']);
				if(!$act) {
					$error = 'Stop playing';
					include('templates/error.php');
				}
				else {
					add_debt($act['id'], $debtor['id'], $_POST['comment'], $amount);
					if(isset($_POST['sendmail'])) {
						$user = get_user_details();
						$token = get_debtor_token($debtor['email']);
						if($token === FALSE) {
							$token = create_debtor_token($debtor['email']);
						}
						email_new_debt($debtor, $user, $act['name'], date('d M Y', strtotime($act['date'])), $_POST['comment'], $amount, $token);
					}
					$success = 'Debt added.';
					include('templates/success.php');
				}
			}
		}
		title('Overview of your activities');
		include('templates/buttons.html');
		$debtors = get_debtors();
		carddeck(get_transactions_per_activity(), 'activities', $debtors);
	}
	else { // $location == 'people' fallback
		title('Overview of your contacts');
		include('templates/buttons.html');
		if(isset($_POST['credit'])) {
			if(!strtotime($_POST['date'])) {
				$error = 'Your credit needs a date.';
				include('templates/error.php');
			}
			elseif(strlen($_POST['comment']) < 2) {
				$error = 'Your credit needs a comment.';
				include('templates/error.php');
			}
			elseif(strlen($_POST['amount']) < 1) {
				$error = 'We need to know how much money you\'ve received.';
				include('templates/error.php');
			}
			elseif(check_debtor($_POST['credit'])) {
				$error = 'Nice try, but that\'s not going to work.';
				include('templates/error.php');
			}
			else {
				$debtor = get_debtor_details($_POST['credit']);
				add_credit($debtor['id'], $_POST['comment'], str_replace(',', '.', $_POST['amount']) * 100, $_POST['date']);
				$success = 'Credit has been added for '. $debtor['email'] . '.';
				include('templates/success.php');
			}
		}
		carddeck(get_transactions_per_debtor(), 'people');
	}
}
elseif($location == 'login') {
	include('templates/emptynav.html');
	
	if(isset($_POST['password'])) {
		if(check_credentials($_POST['email'], $_POST['password'])) {
			$_SESSION['tabby_loggedin'] = $_POST['email'];
			$success = 'You\'ve been logged in.';
			$redirect = 'people';
			include('templates/success.php');
		}
		else {
			$error = 'Incorrect login credentials.';
			include('templates/error.php');
			include('templates/login.html');
		}
	}
	else {
		include('templates/login.html');
	}
}
elseif($location == 'register') {
	include('templates/emptynav.html');
	if(isset($_POST['register'])) {
		if(strlen($_POST['name']) < 5) {
			$error = 'Your name seems oddly short, are you quite sure you didn\'t misspell it?';
		}
		elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$error = 'That\'s not a real email address!';
		}
		elseif(strlen($_POST['iban']) < 12) {
			$error = 'An IBAN is for sure longer than that. Or do you prefer that people just won\'t pay?';
		}
		elseif(strlen($_POST['password']) < 8) {
			$error = 'Let\'s be realistic, a proper password isn\'t that short.';
		}
		elseif(user_exists($_POST['email'])) {
			$error = 'It seems we already know about you, that\'s weird!';
		}
		else {
			register_user($_POST['email'], $_POST['name'], $_POST['password'], $_POST['iban']);
			$success = 'Your registration has been recorded. You should receive an email confirmation soon.';
			include('templates/success.php');
		}
		if(isset($error)) {
			include('templates/error.php');
			include('templates/register.html');
		}
	}
	else {
		include('templates/register.html');
	}
}
elseif(substr($location, 0, 8) == 'confirm/') {
	include('templates/emptynav.html');
	$split = explode('/', $location);
	if(user_email_confirm($split[1])) {
		$success = 'Your confirmation has been processed and the owner of this Tabby instance has been notified. It is now up to the owner to fully approve your account.';
		include('templates/success.php');
	}
	else {
		$error = 'Never heard of that confirmation code.';
		include('templates/error.php');
	}
}
elseif(substr($location, 0, 13) == 'adminconfirm/') {
	include('templates/emptynav.html');
	$error = 'Revisit this link when logged in.';
	include('templates/error.php');
}
elseif($location == 'requesttoken') {
	include('templates/emptynav.html');
	include('resources/people.php');
	if(isset($_POST['email'])) {
		$token = get_debtor_token($_POST['email']);
		if($token !== FALSE) {
			mail_token($_POST['email'], $token);
		}
		$success = 'If you are a known contact for any of our users, you will receive an email containing the link for your debt and credit overview.';
		include('templates/success.php');
	}
	else {
		$error = 'You seem to be at the wrong place somehow.';
		include('templates/error.php');
	}
}
elseif(substr($location, 0, 6) == 'token/') {
	include('templates/tokennav.php');
	include('resources/uiblocks.php');
	include('resources/transactions.php');
	include('resources/people.php');
	
	$split = explode('/', $location);
	$token = $split[1];
	$email = get_token_email($token);
	if($email === FALSE) {
		$error = 'Go away.';
		include('templates/error.php');
	}
	else {
		if(isset($_GET['reset'])) {
			$newtoken = reset_token($token);
			mail_token($email, $newtoken);
			$success = 'Your token has been reset, a new link has been email to you. This current link will also stop working.';
			include('templates/success.php');
		}
		elseif(isset($split[2]) AND $split[2] == 'detail') {
			title('Detailed view');
			detailcard(get_user_transactions_for_debtor($split[3], $email), 'user');
		}
		else {
			if(empty(get_transactions_per_user($email))) {
				$success = 'No one has any debt for you.';
				include('templates/success.php');
			}
			carddeck(get_transactions_per_user($email), 'user');
		}
	}
	
}
else {
	include('templates/index.html');
}
include('templates/footer.php');
