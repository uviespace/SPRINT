<h2>TM Header</h2>

<button id="create_tmheader_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create TM Header Element
</button>

<table id="table_tmheader" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Parameter</th>
			<th>Order</th>
			<th>Role</th>
			<th>Group</th>
			<th>Repetition</th>
			<th>Value</th>
			<th>Description</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_tmheader_row">
	<tr>
		<td></td>
		<td></td>
		<td></td>
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

<div id="tmheader_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit TC Header</h3>
		</div>

		<div class="modal-body">
			<label for="parameter">Parameter:</label>
			<select name="parameter" id="edit_tmheader_parameter" class="form-input modal-input">
				<?php foreach($parameter_values as $param): ?>
					<option value="<?=$param["id"]?>"><?=$param["parameter"]?></option>
				<?php endforeach; ?>
			</select>
			

			<label for="order">Order:</label>
			<input class="form-input modal-input" id="edit_tmheader_order" type="number" name="order" />

			<label for="role">Role:</label>
			<select name="role" id="edit_tmheader_role" class="form-input modal-input">
				<?php foreach($role_values as $role): ?>
					<option value="<?=$role["id"]?>"><?=$role["name"]?></option>
				<?php endforeach; ?>
			</select>

			<label for="group">Group:</label>
			<input class="form-input modal-input" id="edit_tmheader_group" type="number" name="group" />

			<label for="repetition">Repetition:</label>
			<input class="form-input modal-input" id="edit_tmheader_repetition" type="number" name="reptition" />

			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_tmheader_value" type="number" name="value" />

			<label for="description">Description:</label>
			<textarea id="edit_tmheader_description" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="tmheader_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
