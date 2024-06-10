<h2><img style="vertical-align: middle;" src="img/projects_64x64.png" />My Projects</h2>


<button id="create_project_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Project
</button>

<table id="table_project" class="table">
	<thead>
		<th>ID</th>
		<th>Name</th>
		<th>Description</th>
		<th>Owner</th>
		<th>Public</th>
		<th>Action</th>
	</thead>
	<tbody>
		
	</tbody>
</table>

<template id="table_project_row">
	<tr>
		<td></td>
		<td></td>
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


<div id="project_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Project</h3>
		</div>

		<div class="modal-body">
			<label for="name">Name:</label>
			<input id="edit_project_name" class="form-input modal-input" type="text" name="name" />

			<label for="desc">Description:</label>
			<textarea id="edit_project_desc" rows="4" class="modal-input"></textarea>

			<label for="owner">Owner:</label>
			<select name="owner" id="edit_project_owner" class="form-input modal-input">
				<?php foreach($users as $user): ?>
					<option value="<?=$user["id"]?>"><?=$user["name"]?> (<?=$user["email"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="public">Public:</label>
			<select name="public" id="edit_project_public" class="form-input modal-input">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select>
		</div>

		<div class="modal-footer">
			<button id="project_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>


