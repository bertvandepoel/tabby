	<div class="container py-5">
		<div class="row">
			<div class="offset-md-3 col-md-6">
				<h2 class="pb-3">Change your profile details</h2>
				<form action="profile" method="post">
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Email address <span data-toggle="tooltip" data-placement="top" title="" data-original-title="This is your account identifier and can't be changed"><span class="fas fa-question-circle"></span></span></label>
						<label class="col-sm-8 col-form-label"><?php echo $filled['email']; ?></label>
					</div>
					<div class="form-group row">
						<label for="name" class="col-sm-4 col-form-label">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="name" name="name" value="<?php echo $filled['name']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="iban" class="col-sm-4 col-form-label">IBAN</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="iban" name="iban" value="<?php echo $filled['iban']; ?>">
						</div>
					</div>
					<div class="form-group row">
						<label for="password" class="col-sm-4 col-form-label">Password</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" id="password" name="password" value="TABBY_DEFAULT_VALUE">
						</div>
					</div>
					<button type="submit" class="btn btn-danger" name="submit">Update profile</button>
				</form>
				<h2 class="pb-3 pt-5">Alias management</h2>
				<form action="profile" method="post">
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Aliases <span data-toggle="tooltip" data-placement="top" title="" data-original-title="Aliases are used to more accurately display your debt under My debt"><span class="fas fa-question-circle"></span></span></label>
						<div class="col-sm-8 mt-1">
							<?php
							if(empty($filled['aliases'])) {
								echo 'You have no aliases<br>';
							}
							else {
								foreach($filled['aliases'] as $alias) {
									echo '<li class="pb-2">' . $alias['email'];
									if(!is_null($alias['unconfirmed'])) {
										echo ' <span data-toggle="tooltip" data-placement="top" title="" data-original-title="This alias is unconfirmed and will therefore not be used for My debt."><span class="fas fa-exclamation-triangle text-warning"></span></span>';
									}
									echo ' <a href="profile/delete_alias/' . urlencode($alias['email']) . '" class="btn btn-outline-danger btn-sm ml-3"><span class="fas fa-trash mr-2"></span>Delete</a></li>';
								}
							}
							?>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-5 offset-md-4">
							<input type="text" class="form-control" id="alias" name="alias">
						</div>
						<div class="col-sm-3">
							<button type="submit" class="btn btn-outline-danger" name="add_alias"><span class="fas fa-plus mr-2"></span>Add</button>
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