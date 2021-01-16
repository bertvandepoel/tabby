	<div class="container py-5">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="card">
					<div class="card-header">
						<?php echo $title; ?>
					</div>
					<div class="card-body">
						<code><?php echo nl2br(htmlentities($config)); ?></code>
					</div>
				</div>
			</div>
		</div>
	</div>