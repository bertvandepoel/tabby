<div class="container py-5">
	<div class="row">
		<div class="col-md-12">
			<h2 class="pb-4">Recurring expenses</h2>
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Amount</th>
						<th scope="col">Frequency</th>
						<th scope="col">Last run</th>
						<th scope="col">Next run</th>
						<th scope="col">Contacts</th>
						<th scope="col">Edit</th>
						<th scope="col">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($recurring as $row) {
						echo '<tr>';
						echo '<td>' . $row['name'] . '</td>';
						echo '<td>' . human_friendly_amount($row['amount'], FALSE, TRUE) . '</td>';
						echo '<td>' . dateintervalstring_to_frequency($row['frequency']) . '</td>';
						echo '<td>' . ((is_null($row['lastrun'])) ? 'never' : $row['lastrun']) . '</td>';
						echo '<td>' . get_nextrun($row['start'], $row['frequency'], $row['lastrun']) . '</td>';
						echo '<td>' . implode(', ', $row['debtors']['name']) . '</td>';
						echo '<td><a href="recurring/edit/' . $row['id'] . '" class="btn btn-outline-danger btn-sm text-nowrap"><span class="fas fa-edit mr-2"></span>Edit</a></td>';
						echo '<td><a href="recurring/delete/' . $row['id'] . '" class="btn btn-outline-danger btn-sm text-nowrap"><span class="fas fa-trash mr-2"></span>Delete</a></td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
			<a href="recurring/add" class="btn btn-danger"><span class="far fa-calendar-plus mr-2"></span>Add new recurring expense</a>
		</div>
	</div>
</div>