<h2>APIDs</h2>

<button id="create_apid_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create APID
</button>

<table id="table_apid" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Address</th>
			<th>Name</th>
			<th>Description</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_apid_row">
	<tr>
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


<div id="apid_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit APID</h3>
		</div>

		<div class="modal-body">
			<label for="address">Address:</label>
			<input class="form-input modal-input" id="edit_apid_address" type="text" name="address" />

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_apid_name" type="text" name="name" />

			<label for="description">Description:</label>
			<textarea id="edit_apid_desc" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="apid_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>


