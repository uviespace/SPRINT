<h4> Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Packet Parameters for Packet <?=$header_info[0]["kind"]?>(<?=$header_info[0]["type"]?>/<?=$header_info[0]["subtype"]?>) <?=$header_info[0]["name"]?> [<?=$header_info[0]["discriminant"]?>]</h2>

<a class="btn" href="sel_packet-derived.php?idProject=<?=$_GET["idProject"]?>&idStandard=<?=$_GET["idStandard"]?>&open=<?=$_GET["idParent"]?>">
	<i class="nf nf-md-arrow_left_circle" style="margin-right: 4px; font-size: 16px;"></i>Back
</a>


<div id="packet_container" style="width: 100%">
	<canvas id="packet_view" class="canvas_view"></canvas>
</div>


<button id="create_param_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Parameter
</button>

<table id="table_params" class="table">
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
	<tbody></tbody>
</table>


<template id="table_params_row">
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

<div id="params_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Parameter</h3>
		</div>

		<div class="modal-body">
			<label for="parameter">Parameter:</label>
			<select name="parameter" id="edit_param_parameter" class="form-input modal-input">
				<?php foreach($parameter_values as $param): ?>
					<option value="<?=$param["id"]?>"><?=$param["parameter"]?></option>
				<?php endforeach; ?>
			</select>

			<label for="order">Order:</label>
			<input id="edit_param_order" class="form-input modal-input" type="number" name="order" />

			<label for="role">Role:</label>
			<select name="role" id="edit_param_role" class="form-input modal-input">
				<?php foreach($role_values as $role): ?>
					<option value="<?=$role["id"]?>"><?=$role["name"]?> (<?=$role["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="group">Group:</label>
			<input id="edit_param_group" class="form-input modal-input" type="number" name="group" />

			<label for="repetition">Repetition:</label>
			<input id="edit_param_repetition" class="form-input modal-input" type="number" name="repetition" />

			<label for="value">Value:</label>
			<input id="edit_param_value" class="form-input modal-input" type="text" name="value" />

			<label for="description">Description:</label>
			<textarea id="edit_param_desc" rows="4" class="modal-input" name="desc"></textarea>
		</div>
		

		<div class="modal-footer">
			<button id="param_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div> 
