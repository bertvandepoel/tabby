<div class="container py-5">
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<h2 class="pb-4">List of all your contacts</h2>
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Email</th>
						<th scope="col">Edit</th>
						<th scope="col">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($debtors as $debtor) {
						echo '<tr>';
						echo '<td>' . $debtor['name'] . '</td>';
						echo '<td>' . $debtor['email'] . '</td>';
						echo '<td><a href="people/list/edit/' . $debtor['email'] . '" class="btn btn-outline-danger btn-sm"><span class="fas fa-edit mr-2"></span>Edit</a></td>';
						echo '<td><a href="people/list/delete/' . $debtor['email'] . '" class="btn btn-outline-danger btn-sm"><span class="fas fa-trash mr-2"></span>Delete</a></td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
			<a href="people/add" class="btn btn-danger"><span class="fas fa-user-plus mr-2"></span>Add person</a>
		</div>
	</div>
</div>