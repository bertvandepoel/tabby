	<div class="container py-5">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<h3  class="pb-1 text-center">Are you sure you want to delete this <?php echo $what; ?>?</h3>
				<h5 class="pb-4 text-center"><?php echo $warning; ?></h5>
				<form action="<?php echo $location;?>" method="post">
					<div class="form-group row">
						<div class="col-sm-4 offset-md-2 text-center">
							<a href="<?php echo $backlink; ?>" class="btn btn-primary">No, take me back</a>
						</div>
						<div class="col-sm-4 text-center">
							<button name="delete" type="submit" class="btn btn-danger"><span class="fas fa-trash mr-2"></span>Yes, delete this <?php echo $what; ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>