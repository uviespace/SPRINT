<h2>Parameters</h2>

<button id="create_parameter_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Parameter
</button>

<table id="table_parameter" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Domain</th>
			<th style="text-align:left;padding-left:2vw;">Name</th>
			<th style="text-align:left;padding-left:2vw;">Short Description</th>
			<th>Kind</th>
			<th>Data-type</th>
			<th>Role</th>
			<th>Multiplicity</th>
			<th>Value</th>
			<th>Unit</th>
			<th>Users</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_parameter_row">
	<tr>
		<td></td>
		<td></td>
		<td style="text-align:left;padding-left:2vw;"></td>
		<td style="text-align:left;padding-left:2vw;"></td>
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


<div id="parameter_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Parameter</h3>
		</div>

		<div class="modal-body">
			<label for="domain">Domain:</label>
			<input class="form-input modal-input" id="edit_parameter_domain" type="text" name="domain" />

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_parameter_name" type="text" name="name" />

			<label for="kind">Kind:</label>
			<select name="kind" id="edit_parameter_kind" class="form-input modal-input">
				<option value="0">Predef (0)</option>
				<option value="1">PcktHdr (1)</option>
				<option value="2">PcktPar (2)</option>
				<option value="3">DpPar (3)</option>
				<option value="4">DpVar (4)</option>
				<option value="5">DpPar Imported (5)</option>
				<option value="6">DpVar Imported (6)</option>
			</select>

			<label for="role">Role:</label>
			<select name="role" id="edit_parameter_role" class="form-input modal-input">
				<?php foreach($role_values as $role): ?>
					<option value="<?=$role["id"]?>"><?=$role["name"]?> (<?=$role["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="short_desc">Short Description:</label>
			<input class="form-input modal-input" id="edit_parameter_short_desc" type="text" name="short_desc" />

			<label for="datatype">Datatype:</label>
			<select name="datatype" id="edit_parameter_datatype" class="form-input modal-input" data-filter="on">
				<?php foreach($datatypes as $type): ?>
					<option value="<?=$type["id"]?>"><?=$type["domain"]?> / <?=$type["name"]?> (<?=$type["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<div id="ref_param_area">
				<label for="reference_param">Reference Parameter</label>
				<select name="reference_param" id="edit_parameter_reference_param" class="form-input modal-input" data-filter="on">
					<?php foreach($parameters as $param): ?>
						<option value="<?=$param["id"]?>"><?=$param["domain"]?> / <?=$param["name"]?> (<?=$param["id"]?>)></option>
					<?php endforeach; ?>
				</select>
			</div>

			<label for="multiplicity">Multiplicity</label>
			<input class="form-input modal-input" id="edit_parameter_multiplicity" type="number" name="multiplicity" />

			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_parameter_value" type="text" name="value" />

			<label for="unit">Unit:</label>
			<input class="form-input modal-input" id="edit_parameter_unit" type="text" name="unit" />

		</div>

		<div class="modal-footer">
			<button id="parameter_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
