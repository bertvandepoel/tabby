<?php

if(!file_exists('config.php')) {
	include('resources/install.php');
	
	include('templates/header.php');
	include('templates/emptynav.html');
	if(isset($_POST['submit'])) {
		$filled = array('db_type_mysql' => 'true', 'db_host' => $_POST['db_host'], 'db_username' => $_POST['db_username'], 'db_password' => $_POST['db_password'], 'db_name' => $_POST['db_name'], 'app_email' => $_POST['app_email'], 'admin_email' => $_POST['admin_email'], 'user_email' => $_POST['user_email'], 'user_name' => $_POST['user_name'], 'user_iban' => $_POST['user_iban'], 'user_password' => $_POST['user_password'], 'base_url' => $_POST['base_url'], 'days' => $_POST['days'], 'cron' => true);
		
		$db_type = 'mysql';
		if($_POST['db_type'] === 'pgsql') {
			$db_type = 'pgsql';
			$filled['db_type_mysql'] = false;
		}
		$cron_type = 'cron';
		if($_POST['cron_type'] === 'webcron') {
			$cron_type = 'webcron';
			$filled['cron'] = false;
		}
		
		if(!filter_var($_POST['app_email'], FILTER_VALIDATE_EMAIL)) {
			$error = 'You need to enter a valid application email address';
			$filled['app_email'] = '';
		}
		elseif(!filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL)) {
			$error = 'You need to enter a valid admin email address';
			$filled['admin_email'] = '';
		}
		elseif(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
			$error = 'You need to enter a valid account email address';
			$filled['user_email'] = '';
		}
		elseif(strlen($_POST['user_name']) < 5) {
			$error = 'The full name for your account seems oddly short, are you quite sure you didn\'t misspell it?';
			$filled['user_name'] = '';
		}
		elseif(strlen($_POST['user_iban']) < 12) {
			$error = 'You need to enter a valid IBAN for your account';
			$filled['user_iban'] = '';
		}
		elseif(strlen($_POST['user_password']) < 8) {
			$error = 'Let\'s be realistic, a proper password isn\'t that short';
			$filled['user_password'] = '';
		}
		elseif(!filter_var($_POST['base_url'], FILTER_VALIDATE_URL)) {
			$error = 'Your base URL is not a valid URL';
			$filled['base_url'] = '';
		}
		elseif(intval($_POST['days']) < 1 OR intval($_POST['days']) > 30) {
			$error = 'Please enter a valid and realistic number of days between reminders';
			$filled['days'] = '';
		}
		else {
			if($db_type === 'pgsql') {
				try {
					$db = new PDO('pgsql:host=' . $_POST['db_host'] . ';dbname=' . $_POST['db_name'], $_POST['db_username'], $_POST['db_password']);
					$dsn = 'pgsql:host=' . $_POST['db_host'] . ';dbname=' . $_POST['db_name'];
				} catch (PDOException $e) {
					$error = 'There was a problem connecting to the database: ' . $e;
				}
			}
			else {
				try {
					$db = new PDO('mysql:host=' . $_POST['db_host'] . ';dbname=' . $_POST['db_name'], $_POST['db_username'], $_POST['db_password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
					$dsn = 'mysql:host=' . $_POST['db_host'] . ';dbname=' . $_POST['db_name'];
				} catch (PDOException $e) {
					$error = 'There was a problem connecting to the database: ' . $e;
				}
			}
		}
		
		if(!isset($error)) {
			if(!create_db($db_type)) {
				$error = 'Could not create database structures, please make sure you have the correct rights and permissions.';
			}
			elseif(!create_first_user($_POST['user_email'], $_POST['user_name'], $_POST['user_password'], $_POST['user_iban'])) {
				$error = 'Could not create first user account. This is quite abnormal considering database structure creation did not error.';
			}
		}
		
		if(isset($error)) {
			include('templates/error.php');
			include('templates/form_install.php');
		}
		else {
			$config = create_config($dsn, $_POST['db_username'], $_POST['db_password'], $_POST['app_email'], $_POST['admin_email'], $_POST['base_url'], $_POST['days'], $cron_type);
			if($config != 'created') {
				$success = 'Installation completed successfully, but the installer could not write your configuration to config.php. Please follow the instructions below to finish installation.';
				include('templates/success.php');
				$title = 'Create a config.php file in the Tabby top directory with the following contents';
				include('templates/box_config.php');
			}
			else {
				$success = 'Installation completed successfully. You can now start using your installation of Tabby.';
				include('templates/success.php');
			}
			if($cron_type === 'cron') {
				$title = 'Don\'t forget to install a cronjob. Below is an example for crontab. You can also add a script to /etc/cron.daily or create a systemd timer.';
				$config = '0 5 * * * php ' . escapeshellarg(realpath('cron.php'));
				include('templates/box_config.php');
			}
			$check_install = check_install($_POST['base_url']);
			if($check_install !== 'OK') {
				$title = 'By default, Tabby uses a .htaccess file with mod_rewrite to support semantic URLs and hide the changelog.txt file';
				$config = '';
				if($check_install === 'doublefail' OR $check_install === 'regfail'){
					$config .= 'It seems visits to semantic URLs aren\'t correctly mapped to index.php. If you are running Apache, please verify that mod_rewrite is enabled and either allow .htaccess files ("AllowOverride All" on the Directory context, "AccessFileName .htaccess" globally) or appropriately move the contents of .htaccess to a VirtualHost or Directory context within your configuration. If you are using nginx, add a fallback to index.php in the appropriate try_files statement. Instructions on how to fall back to index.php are easily available online for other webserver software.';
				}
				if($check_install === 'doublefail') {
					$config .= "\n\n" . 'It also seems changelog.txt is publicly available. While this isn\'t a problem in and by itself, the changelog clearly indicates which version of Tabby you are running. If any security issue would turn up in the future, a possible attacker could deduce whether you are vulnerable or not based on the changelog when it\'s available. To prevent this, changelog.txt is redirected to index.php. This also means that you can followed identical instructions for Apache. If .htaccess works correctly or its contents are moved to configuration in the right context, both issues should be resolved. If you are using nginx, a rewrite or return can be used prior to try_files to move visits for changelog.txt to the Tabby base URL. Instructions on how to redirect a specific file are easily available online for other webserver software.';
				}
				elseif($check_install === 'changelogfail') {
					$config .= 'It seems changelog.txt is publicly available. While this isn\'t a problem in and by itself, the changelog clearly indicates which version of Tabby you are running. If any security issue would turn up in the future, a possible attacker could deduce whether you are vulnerable or not based on the changelog when it\'s available. To prevent this, changelog.txt is redirected to index.php. If you are running Apache, it is quite unusual that the mod_rewrite rules are working correctly for semantic URLs but not to protect changelog.txt. If you have made changes to .htaccess or to your Apache configuration, then please check these in further detail. If you are using nginx, a rewrite or return can be used prior to try_files to move visits for changelog.txt to the Tabby base URL. Instructions on how to redirect a specific file are easily available online for other webserver software.';
				}
				include('templates/box_config.php');
			}
		}	
	}
	else {
		$base_url = 'http://';
		if(isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] === 'on') {
			$base_url = 'https://';
		}
		$base_url .= $_SERVER['HTTP_HOST'];
		// This is the simplest way to strip index.php?something#something from the current request URI
		// see https://stackoverflow.com/questions/6283071/in-php-is-there-a-simple-way-to-get-the-directory-part-of-a-uri
		$base_url .= preg_replace('{/[^/]+$}','/', $_SERVER['REQUEST_URI']);
		$filled = array('db_type_mysql' => true, 'db_host' => 'localhost', 'db_username' => '', 'db_password' => '', 'db_name' => '', 'app_email' => 'no-reply@' . $_SERVER['HTTP_HOST'], 'admin_email' => '', 'user_email' => '', 'user_name' => '', 'user_iban' => '', 'user_password' => '', 'base_url' => $base_url, 'days' => 5, 'cron' => true);
		include('templates/form_install.php');
	}
	include('templates/footer.php');
	exit;
}

include('config.php');

$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, parse_url($base_url, PHP_URL_PATH)) === 0) {
    $local_uri = substr($uri, strlen(parse_url($base_url, PHP_URL_PATH)));
}
$local_uri = strtok($local_uri, '?');
$location = rtrim($local_uri, '/');

include('templates/header.php');
include('resources/init.php');
include('resources/users.php');

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
					$finstate = get_debtor_financial_state($debtor['email']);
					if(isset($_POST['sendmail']) AND $_POST['sendmail'] == 1) {
						$token = get_debtor_token($debtor['email']);
						if($token === FALSE) {
							$token = create_debtor_token($debtor['email']);
						}
						email_new_debt($debtor, $user, $_POST['name'], date('d M Y', strtotime($_POST['date'])), $_POST['comment'][$i], $amount, $finstate['total'], $token);
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
		include('templates/button_merge.html');
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
		else {
			title('Your debt with other users');
			include('templates/button_merge.html');
			carddeck(get_transactions_per_user($_SESSION['tabby_loggedin']), 'user');
		}
	}
	elseif($location == 'merge') {
		if(isset($_POST['merge'])) {
			if(check_debtor($_POST['merge'])) {
				$error = 'Nice try, but that\'s not going to work.';
				include('templates/error.php');
			}
			else {
				$debt_with_loggedin = get_user_transactions_for_debtor($_SESSION['tabby_loggedin'], $_POST['merge']);
				$debt_with_other = get_user_transactions_for_debtor($_POST['merge'], $_SESSION['tabby_loggedin']);
				if($debt_with_loggedin['total'] < 0 AND $debt_with_other['total'] < 0) {
					$amount = -max($debt_with_loggedin['total'], $debt_with_other['total']);
					$debtor = get_debtor_details($_SESSION['tabby_loggedin'], $_POST['merge']);
					add_credit($debtor['id'], $_POST['mergemessage'], $amount, date('Y-m-d'));
					$debtor = get_debtor_details($_POST['merge']);
					add_credit($debtor['id'], $_POST['mergemessage'], $amount, date('Y-m-d'));
					
					$success = 'The debt with ' . $debtor['name'] . '(' . $debtor['email'] . ') has been merged.';
					include('templates/success.php');
				}
				else {
					$error = 'You have no mutual debt with this contact or the mutual debt has already been processed.';
					include('templates/error.php');
				}
			}
		}
		$mergeable = array();
		foreach(get_transactions_per_user($_SESSION['tabby_loggedin']) as $otheruser => $debt_with_other) {
			if(!isset($debt_with_other['total']) OR $debt_with_other['total'] >= 0) {
				// other user has no debt for currently logged in user
				continue;
			}
			$debt_with_loggedin = get_user_transactions_for_debtor($_SESSION['tabby_loggedin'], $otheruser);
			if($debt_with_loggedin['total'] < 0) {
				$debt_after_merge = 'You';
				if($debt_with_loggedin['total'] < $debt_with_other['total']) {
					$debt_after_merge = $debt_with_other['name'];
				}
				elseif($debt_with_loggedin['total'] == $debt_with_other['total']) {
					$debt_after_merge = 'No one';
				}
				$mergeable[$otheruser] = array('name' => $debt_with_other['name'], 'other_debt' => $debt_with_other['total'], 'loggedin_debt' => $debt_with_loggedin['total'], 'debt_after_merge' => $debt_after_merge);
			}
		}
		if(!empty($mergeable)) {
			include('templates/table_merge.php');
		}
		else {
			$success = 'You have no mergeable debt. This means that anyone you owe doesn\'t simultaneously owe you on this instance of Tabby.';
			include('templates/success.php');
		}
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
					$finstate = get_debtor_financial_state($debtor['email']);
					if(isset($_POST['sendmail'])) {
						$user = get_user_details();
						$token = get_debtor_token($debtor['email']);
						if($token === FALSE) {
							$token = create_debtor_token($debtor['email']);
						}
						email_new_debt($debtor, $user, $act['name'], date('d M Y', strtotime($act['date'])), $_POST['comment'], $amount, $finstate['total'], $token);
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

if($webcron) {
	include('cron.php');
}