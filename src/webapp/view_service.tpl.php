<h2>Services</h2>

<button id="create_service_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Service
</button>

<table id="table_service" class="table">
	<thead>
		<th>ID</th>
		<th>Type</th>
		<th>Name</th>
		<th>Description</th>
		<th>Action</th>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_service_row">
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


<div id="service_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Service</h3>
		</div>

		<div class="modal-body">
			<label for="type">Type:</label>
			<input class="form-input modal-input" id="edit_service_type" type="text" name="type" />

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_service_name" type="text" name="name" />

			<label for="description">Description:</label>
			<textarea id="edit_service_desc" rows="4" class="modal-input"></textarea> 
		</div>

		<div class="modal-footer">
			<button id="service_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div> 
