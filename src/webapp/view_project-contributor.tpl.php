<h2>Project <?=$project?></h2>

<h4>Contributors</h4>


<button id="create_contributor_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Add Contributor
</button>

<table id="table_contributor" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Email</th>
			<th>Role</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>


<template id="table_contributor_row">
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<div class="btn-group">
				<button><i class="nf nf-cod-edit"></i></button>
				<button><i class="nf nf-md-delete_outline"></i></button>
			</div>
		</td>
	</tr>
</template>


<div id="contributor_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Contributor</h3>
		</div>

		<div class="modal-body">

			<label for="email">Email:</label>
			<select name="email" id="edit_contributor_email" class="form-input modal-input">
				<?php foreach($emails as $email): ?>
					<option value="<?=$email["id"]?>"><?=$email["email"]?></option>
				<?php endforeach; ?>
			</select>
			
			<label for="role">Role:</label>
			<select name="role" id="edit_contributor_role" class="form-input modal-input">
				<option value="3">Contributor (3)</option>
				<option value="4">Guest (4)</option>
			</select>
		</div>

		<div class="modal-footer">
			<button id="contributor_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
