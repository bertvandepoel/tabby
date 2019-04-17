	<div class="container py-5">
		<div class="row">
			<div class="offset-md-3 col-md-6">
				<h2 class="pb-4">Add a new contact to track debt for</h2>
				<form action="people/add" method="post">
					<div class="form-group row">
						<label for="name" class="col-sm-2 col-form-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" placeholder="Jan Janssen" value="<?php echo $filled['name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="email" class="col-sm-2 col-form-label">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" placeholder="jan@janssen.be" value="<?php echo $filled['email']; ?>">
						</div>
					</div>
					<button name="add" type="submit" class="btn btn-danger"><span class="fas fa-user-plus mr-2"></span>Add new person</button>
				</form>
			</div>
		</div>
	</div>