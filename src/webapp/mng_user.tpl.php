<h2><img style="vertical-align: middle;" src="img/users_64x64.png" /> Users</h2>

<button id="create_user_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create User
</button>

<table id="table_user" class="table">
	<thead>
		<th>ID</th>
		<th>Name</th>
		<th>Email</th>
		<th>Action</th>
	</thead>
	<tbody>
		
	</tbody>
</table>

<template id="table_user_row">
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


<div id="user_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit User</h3>
		</div>

		<div class="modal-body">
			<label for="name">Name:</label>
			<input id="edit_user_name" class="form-input modal-input" type="text" name="name" />

			<label for="name">Email:</label>
			<input id="edit_user_email" class="form-input modal-input" type="text" name="email" />
		</div>

		<div class="modal-footer">
			<button id="user_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>

