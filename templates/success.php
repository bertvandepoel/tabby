	<div class="container py-5">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="alert alert-success" role="alert">
					<strong>Success</strong> <?php echo $success; ?>
					<?php 
					if(isset($redirect)) {
						echo '<br><br>You will be redirected soon. If not click 
					<a href="' . $redirect . '" class="alert-link">here</a>.'; 
					} ?>
				</div>
			</div>
		</div>
	</div>
<?php 
if(isset($redirect)) {
	echo '<script type="text/javascript">setTimeout("location.href = \'' . $redirect . '\';", 1000);</script>';
}