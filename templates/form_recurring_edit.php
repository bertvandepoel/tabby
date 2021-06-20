	<div class="container py-5">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<h2 class="pb-4">Edit recurring expense</h2>
				<form action="<?php echo $location; ?>" method="post">
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
							<input type="text" class="form-control" id="inlineFormInputGroup" placeholder="13.20" name="amount" value="<?php echo human_friendly_amount($filled['amount'], FALSE); ?>">
						</div>
					</div>
					<div class="form-group row pt-2 pb-1">
						<label class="col-sm-2">Frequency</label>
						<div class="col-sm-10">
							<?php echo dateintervalstring_to_frequency($filled['frequency']); ?>
						</div>
					</div>
					
					<div class="form-group row" id="more-div">
						<label for="debtor" class="col-sm-2 col-form-label">Contact(s)</label>
						<div class="col-sm-5">
							<select class="form-control" name="debtor[]" id="debtor">
								<option>Select a contact</option>
								<?php
								foreach($debtors as $debtor) {
									if($debtor['id'] == $filled['debtors']['id'][0]) {
										echo '<option value="' . $debtor['email'] . '" selected>' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
									}
									else {
										echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
									}
								}
								array_shift($filled['debtors']['id']);
								?>
							</select>
						</div>
						<div class="col-sm-5">
							<button id="more-lines" class="btn btn-secondary" type="button"><span class="fas fa-plus-circle mr-2"></span>More contacts</button>
						</div>
					</div>
					
					<?php foreach($filled['debtors']['id'] as $selected_debtor) { ?>
					<div class="form-group row">
						<div class="col-sm-5 offset-md-2">
							<select class="form-control" name="debtor[]" id="debtor">
								<option>Select a contact</option>
								<?php
								foreach($debtors as $debtor) {
									if($debtor['id'] == $selected_debtor) {
										echo '<option value="' . $debtor['email'] . '" selected>' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
									}
									else {
										echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
					<?php } ?>
					
					<button name="edit" type="submit" class="btn btn-danger my-3"><span class="far fa-calendar-check mr-2"></span>Save recurring expense</button>
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