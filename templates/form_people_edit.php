	<div class="container py-5">
		<div class="row">
			<div class="offset-md-3 col-md-6">
				<h2 class="pb-4">Edit existing contact</h2>
				<form action="<?php echo $location; ?>" method="post">
					<div class="form-group row">
						<label for="name" class="col-sm-2 col-form-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" value="<?php echo $debtor['name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="email" class="col-sm-2 col-form-label">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" value="<?php echo $debtor['email']; ?>">
						</div>
					</div>
					<button name="edit" type="submit" class="btn btn-danger" value="<?php echo $debtor['email']; ?>"><span class="fas fa-edit mr-2"></span>Change contact</button>
				</form>
			</div>
		</div>
	</div>