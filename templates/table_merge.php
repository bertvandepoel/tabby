<div class="container py-5">
	<div class="row">
		<div class="col-md-12">
			<h2 class="pb-4">Merge debt between users</h2>
			<p>If you and another user on this instance of Tabby both have debt with each other, it's possible to merge both debts. In that case, the largest debt will be topped off with the smallest debt, diminishing the smaller debt. This will be indicated by a credit line with the merge message.</p>
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Contact</th>
						<th scope="col">Their debt</th>
						<th scope="col">Your debt</th>
						<th scope="col">Who has debt after merge?</th>
						<th scope="col">Merge message</th>
						<th scope="col">Merge</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($mergeable as $otheremail => $mergedetail) {
						echo '<tr>';
						echo '<form action="merge" method="post">';
						echo '<td>' . $mergedetail['name'] . ' (' . $otheremail . ')</td>';
						echo '<td>' . sprintf("%+.2f", number_format($mergedetail['loggedin_debt']/100, 2)) . '</td>';
						echo '<td>' . sprintf("%+.2f", number_format($mergedetail['other_debt']/100, 2)) . '</td>';
						echo '<td>' . $mergedetail['debt_after_merge'] . '</td>';
						echo '<td><input name="mergemessage" type="text" class="form-control" value ="Merge mutual debt"></td>';
						echo '<td><button name="merge" type="submit" class="btn btn-outline-danger btn-sm" value="' . $otheremail . '"><span class="fas fa-exchange-alt mr-2"></span>Merge</button></td>';
						echo '</form>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>