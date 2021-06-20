	<div class="container py-5">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<h2 class="pb-4">Add a new recurring expense</h2>
				<form action="recurring/add" method="post">
					<div class="form-group row">
						<label for="name" class="col-sm-2 col-form-label">Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="name" name="name" placeholder="Shared internet subscription" value="<?php echo $filled['name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="amount" class="col-sm-2 col-form-label">Amount</label>
						<div class="col-sm-3 input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><?php echo $currency; ?></div>
							</div>
							<input type="text" class="form-control" id="inlineFormInputGroup" placeholder="13.20" name="amount" value="<?php echo $filled['amount']; ?>">
						</div>
					</div>
					<div class="form-group row pt-2 pb-1">
						<label class="col-sm-2">Frequency</label>
						<div class="col-sm-10">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" id="freq_weekly" name="frequency" value="weekly" <?=($filled['frequency'] == 'weekly')?'checked':'';?>>
								<label class="form-check-label" for="freq_weekly">Weekly</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" id="freq_monthly" name="frequency" value="monthly" <?=($filled['frequency'] == 'monthly')?'checked':'';?>>
								<label class="form-check-label" for="freq_monthly">Monthly</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" id="freq_yearly" name="frequency" value="yearly" <?=($filled['frequency'] == 'yearly')?'checked':'';?>>
								<label class="form-check-label" for="freq_yearly">Yearly</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" id="freq_days" name="frequency" value="days" <?=($filled['frequency'] == 'days')?'checked':'';?>>
								<input type="text" class="form-control form-check-inline w-25 mr-2 text-center" name="frequency_days" id="frequency_days" placeholder="14" value="<?php echo $filled['frequency_days']; ?>" onfocus="document.getElementById('freq_days').checked = true">
								<label class="form-check-label" for="freq_days">days</label>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label for="date" class="col-sm-2 col-form-label">First run <span data-toggle="tooltip" data-placement="top" title="" data-original-title="On this date your recurring expense will run for the first time, and frequency is offset from that date on."><span class="fas fa-question-circle"></span></span></label>
						<div class="col-sm-3">
							<input type="date" class="form-control" id="date" name="date" value="<?php echo $filled['date']; ?>">
						</div>
						<div class="col-sm-2">
							<button id="today" class="btn btn-secondary" type="button" onclick="document.getElementById('date').value = '<?php echo date('Y-m-d'); ?>'"><span class="fas fa-calendar-check mr-2"></span>Now</button>
						</div>
					</div>
					
					<div class="form-group row" id="more-div">
						<label for="debtor" class="col-sm-2 col-form-label">Contact(s)</label>
						<div class="col-sm-5">
							<select class="form-control" name="debtor[]" id="debtor">
								<option>Select a contact</option>
								<?php
								foreach($debtors as $debtor) {
									echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
								}
								?>
							</select>
						</div>
						<div class="col-sm-5">
							<button id="more-lines" class="btn btn-secondary" type="button"><span class="fas fa-plus-circle mr-2"></span>More contacts</button>
						</div>
					</div>
					
					<button name="add" type="submit" class="btn btn-danger my-3"><span class="far fa-calendar-plus mr-2"></span>Add new recurring expense</button>
				</form>
			</div>
		</div>
	</div>
	
	<div class="d-none" id="copyme">
		<div class="form-group row">
			<div class="col-sm-5 offset-md-2">
				<select class="form-control" name="debtor[]">
					<option>Select a contact</option>
					<?php
					foreach($debtors as $debtor) {
						echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
					}
					?>
				</select>
			</div>
		</div>
	</div>
	
	<script src="bubblegum/js/jquery-3.3.1.slim.min.js"></script>
	<script>
	    $(function () {
	        $('[data-toggle="tooltip"]').tooltip()
	    })
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#more-lines").click(function(){ 
				var html = $("#copyme").html();
				$("#more-div").after(html);
			});
		});
	</script>