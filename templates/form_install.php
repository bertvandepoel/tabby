	<div class="container py-5">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<h1>Install Tabby</h2>
				<p>It seems there's currently no configuration file available, so Tabby was unable to connect to your database. Please enter your configuration details below so the correct file can be created for you or, if no write permissions are available, printed so you can install it yourself.</p>
				<form action="?" method="post">
					<h3>Database configuration</h3>
					<div class="form-group row">
						<legend class="col-form-label col-sm-4">Database software</legend>
						<div class="col-sm-8">
							<div class="form-check form-check-inline">
								<input class="form-check-input form-control" type="radio" id="db_type_mysql" name="db_type" value="mysql" <?=($filled['db_type_mysql'])?'checked':'';?>>
								<label class="form-check-label" for="db_type_mysql">MySQL</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input form-control" type="radio" id="db_type_pgsql" name="db_type" value="pgsql" <?=(!$filled['db_type_mysql'])?'checked':'';?>>
								<label class="form-check-label" for="db_type_pgsql">PostgreSQL</label>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label for="db_host" class="col-sm-4 col-form-label">Host (FQDN, hostname or IP)</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="db_host" name="db_host" value="<?php echo $filled['db_host']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="db_username" class="col-sm-4 col-form-label">Username</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="db_username" name="db_username" value="<?php echo $filled['db_username']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="db_password" class="col-sm-4 col-form-label">Password</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="db_password" name="db_password" value="<?php echo $filled['db_password']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="db_name" class="col-sm-4 col-form-label">Database name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="db_name" name="db_name" value="<?php echo $filled['db_name']; ?>">
						</div>
					</div>
					<p>Note: If you are using socket authentication based on the user running your PHP CGI or FPM, enter localhost as your host for MySQL or the location of the socket as host (e.g. /var/run/postgresql) for PostgreSQL, enter the username and leave password blank.</p>
					<h3>Email configuration</h3>
					<div class="form-group row">
						<label for="app_email" class="col-sm-4 col-form-label">Application email address <span data-toggle="tooltip" data-placement="top" title="" data-original-title="This email address is used when sending reminders and confirmation emails."><span class="fas fa-question-circle"></span></span></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="app_email" name="app_email" value="<?php echo $filled['app_email']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="admin_email" class="col-sm-4 col-form-label">Admin email address <span data-toggle="tooltip" data-placement="top" title="" data-original-title="This email address receives notifications for new registrations to approve or deny them. It can be a forwarder not connected to any account."><span class="fas fa-question-circle"></span></span></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="admin_email" name="admin_email" value="<?php echo $filled['admin_email']; ?>">
						</div>
					</div>
					<p>Please refer to the <a href="https://www.php.net/manual/en/mail.configuration.php" target="_blank">PHP documentation</a> and adapt your php.ini or .user.ini if standard sendmail emailing isn't available.</p>
					<h3>Your account details</h3>
					<div class="form-group row">
						<label for="user_email" class="col-sm-4 col-form-label">Email address</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_email" name="user_email" value="<?php echo $filled['user_email']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="user_name" class="col-sm-4 col-form-label">Full name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo $filled['user_name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="user_iban" class="col-sm-4 col-form-label">IBAN</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_iban" name="user_iban" value="<?php echo $filled['user_iban']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="user_password" class="col-sm-4 col-form-label">Password</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" id="user_password" name="user_password" value="<?php echo $filled['user_password']; ?>">
						</div>
					</div>
					<p>Note: It's only possible to approve or deny new users after logging in. This email address can however be different from the admin email address, e.g. when you work with a forwarder for several admins.</p>
					<h3>Other configuration</h3>
					<div class="form-group row">
						<label for="base_url" class="col-sm-4 col-form-label">Base URL</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="base_url" name="base_url" value="<?php echo $filled['base_url']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="days" class="col-sm-4 col-form-label">Days between reminders</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="days" name="days" value="<?php echo $filled['days']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<legend class="col-form-label col-sm-4">Reminder cron</legend>
						<div class="col-sm-8">
							<div class="form-check form-check-inline">
								<input class="form-check- form-control" type="radio" id="cron" name="cron_type" value="cron" <?=($filled['cron'])?'checked':'';?>>
								<label class="form-check-label" for="cron">Cron</label> <span data-toggle="tooltip" data-placement="top" title="" data-original-title="You need to configure a cronjob using your hosting control panel, the crontab command, a file in /etc/cron.daily or a systemd timer."><span class="fas fa-question-circle pl-1"></span></span>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input form-control" type="radio" id="webcron" name="cron_type" value="webcron" <?=(!$filled['cron'])?'checked':'';?>>
								<label class="form-check-label" for="webcron">Webcron</label> <span data-toggle="tooltip" data-placement="top" title="" data-original-title="This is a fallback option where every visit to Tabby triggers a check for the reminder cronjob, regular cron is advised."><span class="fas fa-question-circle pl-1"></span></span>
							</div>
						</div>
					</div>
					<p>Keep in mind the days between reminders are to remind those who have an account to check their bank statements and then issue reminders through the interface. Tabby won't automatically remind people if the owner of the debt isn't checking their bank account to prevent spam.</p>
					<div class="form-group row">
						<div class="col-sm-12 d-flex justify-content-center pt-3">
							<button type="submit" class="btn btn-danger" name="submit"><span class="fas fa-file-import mr-2"></span>Import database and generate config</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="bubblegum/js/jquery-3.3.1.slim.min.js"></script>
	<script>
	    $(function () {
	        $('[data-toggle="tooltip"]').tooltip()
	    })
	</script>