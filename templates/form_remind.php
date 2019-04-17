	<div class="container py-5">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<h2 class="pb-2 text-center">Send reminder email</h2>
				<h5 class="pb-4">You've last sent a reminder to someone on <?php if(is_null($user['reminddate'])) { echo 'hmmm, actually, never'; } else { echo date('d M Y', strtotime($user['reminddate'])); } ?>.</h5>
				<form action="remind" method="post">
					<div class="form-group row">
						<label for="debtor" class="col-sm-4 col-form-label">Send a reminder to</label>
						<div class="col-sm-8">
							<select class="form-control" name="debtor">
								<option value="TABBY_REMIND_EVERYONE">everyone with debt</option>	
								<?php
								foreach($debtors as $debtor) {
									echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="comment" class="col-sm-4 col-form-label"> with the following message </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="iban" name="comment" placeholder="(optional)">
						</div>
					</div>
					<button type="submit" class="btn btn-danger" name="submit"><span class="fas fa-envelope mr-2"></span>Send reminder(s)</button>
				</form>
			</div>
		</div>
	</div>