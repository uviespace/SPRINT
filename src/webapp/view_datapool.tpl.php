<h2>Datapool</h2>

<button id="create_datapool_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Datapool
</button>


<table id="table_datapool" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Domain</th>
			<th>Name</th>
			<th>Short Description</th>
			<th>Kind</th>
			<th>Datatype</th>
			<th>Multiplicity</th>
			<th style="max-width: 120px;">Value</th>
			<th>Unit</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>	
	</tbody>
</table>


<template id="table_datapool_row">
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="max-width: 120px; overflow: hidden; text-overflow: ellipsis;"></td>
		<td></td>
		<td>
			<div class="btn-group">
				<button><i class="nf nf-cod-edit"></i></button>
				<button><i class="nf nf-md-delete_outline"></i></button>
			</div>
		</td>
	</tr>
</template>

<div id="datapool_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Datapool Item</h3>
		</div>

		<div class="modal-body">
			<label for="domain">Domain:</label>
			<input class="form-input modal-input" id="edit_datapool_domain" type="text" name="domain" />

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_datapool_name" type="text" name="name" />

			<label for="short_desc">Short Description</label>
			<input class="form-input modal-input" id="edit_datapool_short_desc" type="text" name="short_desc" />

			<label for="kind">Kind:</label>
			<select name="kind" id="edit_datapool_kind" class="form-input modal-input">
				<option value="3">DpPar (3)</option>
				<option value="4">DpVar (4)</option>
				<option value="5">DpPar Imp (5)</option>
				<option value="6">DpVar Imp (6)</option>
			</select>

			<label for="datatype">Datatype:</label>
			<select name="datatype" id="edit_datapool_datatype" class="form-input modal-input">
				<?php foreach($datatypes as $type): ?>
					<option value="<?=$type["id"]?>"><?=$type["domain"]?> / <?=$type["name"]?> (<?=$type["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="multiplicity">Multiplicity</label>
			<input class="form-input modal-input" id="edit_datapool_multiplicity" type="number" name="multiplicity" />

			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_datapool_value" type="text" name="value" />

			<label for="unit">Unit:</label>
			<input class="form-input modal-input" id="edit_datapool_unit" type="text" name="unit" />
		</div>

		<div class="modal-footer">
			<button id="datapool_submit_button" class="btn-submit"></button>
		</div>		
	</div>
</div>
