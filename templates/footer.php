<?php
	if(date('Y') > 2019) {
		$year = '2019 - ' . date('Y');
	}
	else {
		$year = 2019;
	}
?>
	<footer class="footer mt-auto py-3">
		<div class="container">
			<p class="text-muted text-center">Tabby &copy; <?php echo $year; ?> Bert Van de Poel</p>
		</div>
	</footer>
	<script src="bubblegum/js/jquery-3.3.1.slim.min.js"></script>
	<script src="bubblegum/js/popper.1.14.7.min.js."></script>
	<script src="bubblegum/js/bootstrap.4.3.1.min.js"></script>
</body>
</html>