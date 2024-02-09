<h2>Constants</h2>


<button id="create_constant_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Constant
</button>

<table id="table_constant" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Domain</th>
			<th>Name</th>
			<th>Value</th>
			<th>Description</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_constant_row">
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

<div id="constant_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Constant</h3>
		</div>

		<div class="modal-body">
			<label for="domain">Domain:</label>
			<input id="edit_constant_domain" class="form-input modal-input" name="domain" />

			<label for="name">Name:</label>
			<input id="edit_constant_name" class="form-input modal-input" name="name" />

			<label for="value">Value:</label>
			<input id="edit_constant_value" class="form-input modal-input" name="value" />

			<label for="description">Description:</label>
			<textarea id="edit_constant_desc" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="constant_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
