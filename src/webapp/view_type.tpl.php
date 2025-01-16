<h2>Datatypes</h2>

<button id="create_datatype_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Datatype
</button>

<table id="table_datatype" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Domain</th>
			<th style="text-align:left;padding-left:2vw;">Name</th>
			<th>Native Type</th>
			<th>Size</th>
			<th>Type/Format</th>
			<th>Default Value</th>
			<th>Users</th>
			<th style="text-align:left;padding-left:2vw;" >Description</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>


<template id="table_datatype_row">
	<tr>
		<td></td>
		<td></td>
		<td style="text-align:left;padding-left:2vw;"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align:left;padding-left:2vw;"></td>
		<td>
			<div class="btn-group">
				<button><i class="nf nf-cod-edit"></i></button>
				<button><i class="nf nf-md-delete_outline"></i></button>
			</div>
		</td>
	</tr>
</template>


<div id="datatype_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Datatype</h3>
		</div>

		<div class="modal-body">
			<label for="domain">Domain:</label>
			<input class="form-input modal-input" id="edit_datatype_domain" type="text" name="domain" />

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_datatype_name" type="text" name="name" />

			<label for="native_type">Native Type:</label>
			<input class="form-input modal-input" id="edit_datatype_native_type" type="text" name="native_type" />

			<label for="size">Size:</label>
			<input class="form-input modal-input" id="edit_datatype_size" type="number" name="size" onchange="update_pus_datatypes()" />

			<label for="format">Param. Type/Format</label>
			<select class="form-input modal-input" id="edit_datatype_pustype">
			</select>


			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_datatype_value" type="text" name="value" />

			<label for="desc">Description:</label>
			<textarea id="edit_datatype_desc" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="datatype_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
