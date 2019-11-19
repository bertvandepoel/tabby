<?php

function title($text) {
	?>
	<div class="container py-4">
		<div class="row">
			<div class="col-md-12">
				<h2><?php echo $text; ?></h2>
			</div>
		</div>
	</div>
	<?php
}

function carddeck($cards, $type, $extra = NULL) {
	echo '<div class="container-fluid">';
	echo '<div class="row my-3">';
	echo '<div class="col-md-12">';
	echo '<div class="card-deck justify-content-center">';
	foreach($cards as $card) {
		if($type == 'people') {
			peoplecard($card);
		}
		elseif($type == 'user') {
			usercard($card);
		}
		else { //activity
			activitycard($card, $extra);
		}
	}
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

function detailcard($card, $type) {
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-md-12">';
	if($type == 'people') {
		big_peoplecard($card);
	}
	elseif($type == 'user') {
		big_usercard($card);
	}
	else {
		big_activitycard($card);
	}
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

function activitycard($card, $debtors) {
	?>
	<div class="card my-2 activitycard">
		<div class="card-header text-center">
			<?php echo $card['name'] ?><span class="badge badge-primary p-2 mr-2 float-left"><?php echo date('d M Y', strtotime($card['date'])); ?></span>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				foreach($card['data'] as $row) {
					$badge = 'light';
					if($row['color'] == 'red') {
						$badge = 'danger';
					}
					elseif($row['color'] == 'orange') {
						$badge = 'warning';
					}
					elseif($row['color'] == 'green') {
						$badge = 'success';
					}
					
					echo '<li class="list-group-item px-2"><span class="badge badge-' . $badge . ' p-2 float-right">' . number_format($row['amount']/100, 2)  . '</span><span class="font-weight-bold">' . $row['name'] . '</span>: ' . $row['comment'] . '</li>';
				}
				?>
			</ul>
		</div>
		<ul class="list-group list-group-flush">
			<li class="list-group-item px-2">
				<form action="activities" method="post">
					<div class="form-row">
						<div class="col-md-4 px-1">
							<select class="form-control" name="debtor">
								<option>Select a contact</option>
								<?php
								foreach($debtors as $debtor) {
									echo '<option value="' . $debtor['email'] . '">' . $debtor['name'] . ' (' . $debtor['email'] . ')</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-4 px-1">
							<input type="text" class="form-control" id="comment" name="comment" placeholder="1 pizza pepperoni + 2 ice teas">
						</div>
						<div class="col-md-2 px-1">
							<input type="text" class="form-control" id="amount" placeholder="13.20" name="amount">
						</div>
						<div class="col-md-1 px-1 align-self-center">
							<div class="form-check form-check-inline">
								<input name="sendmail" type="checkbox" class="form-check-input" id="mail-checkbox-<?php echo $card['id']; ?>"> <label class="form-check-label" for="mail-checkbox-<?php echo $card['id']; ?>"><span class="fas fa-envelope" title="notify contact"></label>
							</div>
						</div>
						<div class="col-md-1 px-1">
							<button name="debt" type="submit" class="btn btn-danger btn-sm mt-1" value="<?php echo $card['id']; ?>"><span class="fas fa-plus-square"></span></button>
						</div>
					</div>
				</form>
			</li>
		</ul>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-12">
					<a href="activities/detail/<?php echo $card['id']; ?>" class="btn btn-outline-danger btn-sm"><span class="fas fa-edit mr-2"></span>Edit</a>
					<a href="activities/delete/<?php echo $card['id']; ?>" class="btn btn-outline-danger btn-sm ml-2"><span class="fas fa-trash mr-2"></span>Delete</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function peoplecard($card) {
	?>
	<div class="card my-2">
		<div class="card-header text-center">
			<?php echo $card['name'] ?>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				if($card['more']) {
					echo '<li class="list-group-item text-center"><a href="people/detail/' . $card['email'] . '" class="btn btn-outline-danger btn-sm">Show all records</a></li>';
				}
				foreach($card['data'] as $row) {
					$badge = 'light';
					if($row['color'] == 'red') {
						$badge = 'danger';
					}
					elseif($row['color'] == 'orange') {
						$badge = 'warning';
					}
					elseif($row['color'] == 'green') {
						$badge = 'success';
					}
					
					echo '<li class="list-group-item px-2"><span class="badge badge-primary p-2 mr-2 float-left">' . date('d M Y', strtotime($row['date'])) . '</span>  <span class="badge badge-' . $badge . ' p-2 float-right">' . sprintf("%+.2f", number_format($row['amount']/100, 2))  . '</span>' . $row['description'] . '</li>';
				}
				?>
			</ul>
		</div>
		<ul class="list-group list-group-flush">
			<li class="list-group-item px-2">
				<form action="people" method="post">
					<div class="form-row">
						<div class="col-md-4 px-1">
							<input type="date" class="form-control" id="<?php echo $card['email']; ?>-date" name="date">
						</div>
						<div class="col-md-1 px-1">
							<button class="btn btn-secondary btn-sm mt-1" type="button" onclick="document.getElementById('<?php echo $card['email']; ?>-date').value = '<?php echo date('Y-m-d'); ?>'"><span class="fas fa-calendar-check"></span></button>
						</div>
						<div class="col-md-4 px-1">
							<input type="text" class="form-control" name="comment" placeholder="Wire transfer">
						</div>
						<div class="col-md-2 px-1">
							<input type="text" class="form-control" placeholder="7.50" name="amount">
						</div>
						<div class="col-md-1 px-1">
							<button name="credit" type="submit" class="btn btn-success btn-sm mt-1" value="<?php echo $card['email']; ?>"><span class="fas fa-plus"></span></button>
						</div>
					</div>
				</form>
			</li>
		</ul>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-6">
					<a href="people/detail/<?php echo $card['email']; ?>" class="btn btn-outline-danger btn-sm"><span class="fas fa-edit mr-2"></span>Edit</a>
				</div>
				<div class="col-md-6 text-right">
				Total: 
				<?php
				if($card['total'] > 0) {
					echo '<span class="text-success">+' . number_format($card['total']/100, 2) . '</span>';
				}
				elseif($card['total'] == 0) {
					echo number_format($card['total']/100, 2);
				}
				else {
					echo '<span class="text-danger">' . number_format($card['total']/100, 2) . '</span>';
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function usercard($card) {
	global $location;
	?>
	<div class="card my-2">
		<div class="card-header text-center">
			<?php echo $card['name'] . ' (' . $card['email'] . ')'; ?>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				if($card['more']) {
					echo '<li class="list-group-item text-center"><a href="' . $location . '/detail/' . $card['user'] . '" class="btn btn-outline-danger btn-sm">Show all records</a></li>';
				}
				foreach($card['data'] as $row) {
					echo '<li class="list-group-item px-2"><span class="badge badge-primary p-2 mr-2 float-left">' . date('d M Y', strtotime($row['date'])) . '</span>  <span class="badge badge-light p-2 float-right">' . sprintf("%+.2f", number_format($row['amount']/100, 2))  . '</span>' . $row['description'] . '</li>';
				}
				?>
			</ul>
		</div>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-6">
					<?php echo $card['iban']; ?>
				</div>
				<div class="col-md-6 text-right">
				Total: 
				<?php
				if($card['total'] > 0) {
					echo '<span class="text-success">+' . number_format($card['total']/100, 2) . '</span>';
				}
				elseif($card['total'] == 0) {
					echo number_format($card['total']/100, 2);
				}
				else {
					echo '<span class="text-danger">' . number_format($card['total']/100, 2) . '</span>';
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function big_peoplecard($card) {
	?>
	<div class="card">
		<div class="card-header text-center">
			<?php echo $card['name'] ?>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				foreach($card['data'] as $row) {
					$badge = 'light';
					if($row['color'] == 'red') {
						$badge = 'danger';
					}
					elseif($row['color'] == 'orange') {
						$badge = 'warning';
					}
					elseif($row['color'] == 'green') {
						$badge = 'success';
					}
					
					echo '<li class="list-group-item px-3"><span class="badge badge-primary p-2 mr-2 float-left">' . date('d M Y', strtotime($row['date'])) . '</span> <a href="people/detail/' . $card['email'] . '?del=' . $row['id'] . '" class="btn btn-outline-danger btn-sm ml-5 float-right"><span class="fas fa-trash-alt"></span></a> <span class="badge badge-' . $badge . ' p-2 float-right">' . sprintf("%+.2f", number_format($row['amount']/100, 2))  . '</span>' . $row['description'] . '</li>';
				}
				?>
			</ul>
		</div>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-12 text-right">
				Total: 
				<?php
				if($card['total'] > 0) {
					echo '<span class="text-success">+' . number_format($card['total']/100, 2) . '</span>';
				}
				elseif($card['total'] == 0) {
					echo number_format($card['total']/100, 2);
				}
				else {
					echo '<span class="text-danger">' . number_format($card['total']/100, 2) . '</span>';
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function big_activitycard($card) {
	?>
	<div class="card my-2">
		<div class="card-header text-center">
			<?php echo $card['name'] ?><span class="badge badge-primary p-2 mr-2 float-left"><?php echo date('d M Y', strtotime($card['date'])); ?></span>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				foreach($card['data'] as $row) {
					$badge = 'light';
					if($row['color'] == 'red') {
						$badge = 'danger';
					}
					elseif($row['color'] == 'orange') {
						$badge = 'warning';
					}
					elseif($row['color'] == 'green') {
						$badge = 'success';
					}
					
					echo '<li class="list-group-item px-2"> <a href="activities/detail/' . $card['id'] . '?del=' . $row['id'] . '" class="btn btn-outline-danger btn-sm ml-5 float-right"><span class="fas fa-trash-alt"></span></a> <span class="badge badge-' . $badge . ' p-2 float-right">' . number_format($row['amount']/100, 2)  . '</span><span class="font-weight-bold">' . $row['name'] . '</span>: ' . $row['comment'] . '</li>';
				}
				?>
			</ul>
		</div>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-12">
					<a href="activities/delete/<?php echo $card['id']; ?>" class="btn btn-outline-danger btn-sm ml-2"><span class="fas fa-trash mr-2"></span>Delete</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function big_usercard($card) {
	?>
	<div class="card my-2">
		<div class="card-header text-center">
			<?php echo $card['name'] . ' (' . $card['email'] . ')'; ?>
		</div>
		<div class="card-body p-0">
			<ul class="list-group list-group-flush">
				<?php
				foreach($card['data'] as $row) {
					echo '<li class="list-group-item px-2"><span class="badge badge-primary p-2 mr-2 float-left">' . date('d M Y', strtotime($row['date'])) . '</span>  <span class="badge badge-light p-2 float-right">' . sprintf("%+.2f", number_format($row['amount']/100, 2))  . '</span>' . $row['description'] . '</li>';
				}
				?>
			</ul>
		</div>
		<div class="card-footer text-muted">
			<div class="row">
				<div class="col-md-6">
					<?php echo $card['iban']; ?>
				</div>
				<div class="col-md-6 text-right">
				Total: 
				<?php
				if($card['total'] > 0) {
					echo '<span class="text-success">+' . number_format($card['total']/100, 2) . '</span>';
				}
				elseif($card['total'] == 0) {
					echo number_format($card['total']/100, 2);
				}
				else {
					echo '<span class="text-danger">' . number_format($card['total']/100, 2) . '</span>';
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}