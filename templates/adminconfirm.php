	<div class="container py-5">
		<div class="row">
			<div class="offset-md-3 col-md-6">
				<h3  class="pb-4 text-center">Would you like to confirm this user?</h3>
				<table class="table">
					<tbody>
						<tr>
							<th>Name</th><td><?php echo $pending['name']; ?></td>
						</tr>
						<tr>
							<th>Email</th><td><?php echo $pending['email']; ?></td>
						</tr>
						<tr>
							<th>IBAN</th><td><?php echo $pending['iban']; ?></td>
						</tr>
						<tr>
							<th>Date</th><td><?php echo date('d M Y, H:i', strtotime($pending['datetime'])); ?></td>
						</tr>
					</tbody>
				</table>
				<form action="<?php echo $location;?>" method="post">
					<div class="form-group row">
						<div class="col-sm-6 text-center">
							<button name="deny" type="submit" class="btn btn-danger"><span class="fas fa-user-slash mr-2"></span>Deny this account</button>
						</div>
						<div class="col-sm-6 text-center">
							<button name="approve" type="submit" class="btn btn-primary"><span class="fas fa-user-check mr-2"></span>Approve this account</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>