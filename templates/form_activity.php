	<div class="container py-5">
		<div class="row">
			<div class="offset-md-1 col-md-10">
				<h2 class="pb-4">Add a new activity</h2>
				<form action="activities/add" method="post">
					<div class="form-group row">
						<label for="name" class="col-sm-1 col-form-label">Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="name" name="name" placeholder="Pizza weekend teambuilding" value="<?php echo $filled['name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="date" class="col-sm-1 col-form-label">Date</label>
						<div class="col-sm-3">
							<input type="date" class="form-control" id="date" name="date" value="<?php echo $filled['date']; ?>">
						</div>
						<div class="col-sm-2">
							<button id="today" class="btn btn-secondary" type="button" onclick="document.getElementById('date').value = '<?php echo date('Y-m-d'); ?>'"><span class="fas fa-calendar-check mr-2"></span>Today</button>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" id="sendmail" name="sendmail" checked>
								<label class="form-check-label" for="sendmail">
									Notify all contacts they have new debt by email
								</label>
							</div>
						</div>
					</div>
					<h4 class="pb-2">Activity debt</h4>
					<div class="card card-body">
						<div class="form-group row">
							<div class="col-sm-4 px-1 pr-3">
								<select class="form-control" name="debtor[]">
									<option>Select a contact</option>
									<?php
									foreach($debtors as $debtor) {
										echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
									}
									?>
								</select>
							</div>
							<label for="comment" class="col-sm-1 col-form-label text-right px-0">Comment</label>
							<div class="col-sm-5 px-2">
								<input type="text" class="form-control" id="comment" name="comment[]" placeholder="1 pizza pepperoni + 2 ice teas">
							</div>
							<div class="col-sm-2 input-group px-1 pl-2">
								<div class="input-group-prepend">
									<div class="input-group-text">€</div>
								</div>
								<input type="text" class="form-control" id="inlineFormInputGroup" placeholder="13.20" name="amount[]">
							</div>
						</div>
						<div class="row" id="more-div">
							<div class="col-sm-12 px-1">
								<button id="more-lines" class="btn btn-secondary" type="button"><span class="fas fa-plus-circle mr-2"></span>More debt</button>
							</div>
						</div>
					</div>
					<button name="add" type="submit" class="btn btn-danger my-3"><span class="fas fa-plus mr-2"></span>Add new activity</button>
				</form>
			</div>
		</div>
	</div>
	
	<div class="d-none" id="copyme">
		<div class="form-group row">
			<div class="col-sm-4 px-1 pr-3">
				<select class="form-control" name="debtor[]">
					<option>Select a contact</option>
					<?php
					foreach($debtors as $debtor) {
						echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
					}
					?>
				</select>
			</div>
			<label for="comment" class="col-sm-1 col-form-label text-right px-0">Comment</label>
			<div class="col-sm-5 px-2">
				<input type="text" class="form-control" id="comment" name="comment[]" placeholder="1 pizza pepperoni + 2 ice teas">
			</div>
			<div class="col-sm-2 input-group px-1 pl-2">
				<div class="input-group-prepend">
					<div class="input-group-text">€</div>
				</div>
				<input type="text" class="form-control" id="inlineFormInputGroup" placeholder="13.20" name="amount[]">
			</div>
		</div>
	</div>
	
	<script src="bubblegum/js/jquery-3.3.1.slim.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#more-lines").click(function(){ 
				var html = $("#copyme").html();
				$("#more-div").before(html);
			});
		});
	</script>