<h4>Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Packet Parameters</h2>


<div class="packet-table">
	<?php foreach($packets as $packet): ?>
		<div class="packet-row">
			<div class="col-open">
				<button class="btn btn-small" onclick="toggle_param_visibility(<?=$packet["id"]?>)">
					<i id="icon-<?=$packet["id"]?>" class="nf nf-cod-triangle_right"></i>
				</button>
			</div>

			<div class="col-id"><?=$packet["id"]?></div>
			<div class="col-type"><?=$packet["domain"]?> / <?=$packet["kind"]?>(<?=$packet["type"]?>/<?=$packet["subtype"]?>)</div>
			<div class="col-name"><?=$packet["name"]?></div>
			<div class="col-param"><?=$packet["param_count"]?> parameter(s)</div>

			<div id="packet-<?=$packet["id"]?>" class="packet-container" style="display: none;">
				<canvas id="packet_view_<?=$packet["id"]?>" class="canvas-view"></canvas>


				<button id="create_param_button_id_<?=$packet["id"]?>" class="btn">
					<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Add Parameter
				</button>
				<table id="table_param_<?=$packet["id"]?>"  class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Parameter</th>
							<th>Order</th>
							<th>Role</th>
							<th>Group</th>
							<th>Repetition</th>
							<th>Value</th>
							<th>Size</th>
							<th>Description</th>
							<th></th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>
</div>


<template id="table_param_row">
	<tr>
		<td></td>
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
				<button onclick="click_up(event)"><i class="nf nf-fa-arrow_up"></i></button>
				<button onclick="click_down(event)"><i class="nf nf-fa-arrow_down"></i></button>
			</div>
		</td>
		<td>
			<div class="btn-group">
				<button><i class="nf nf-cod-edit"></i></button>
				<button><i class="nf nf-md-delete_outline"></i></button>
			</div>
		</td>
	</tr>
</template>

<div id="param_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Parameter</h3>
		</div>

		<div class="modal-body">
			<label for="parameter">Parameter:</label>
			<select name="parameter" id="edit_parameter_parameter" class="form-input modal-input">
				<?php foreach($parameter_values as $param): ?>
					<option value="<?=$param["id"]?>"><?=$param["parameter"]?></option>
				<?php endforeach; ?>
			</select>

			<label for="role">Role:</label>
			<select name="role" id="edit_parameter_role" class="form-input modal-input">
				<?php foreach($role_values as $role): ?>
					<option value="<?=$role["id"]?>"><?=$role["name"]?> (<?=$role["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="group">Group:</label>
			<input class="form-input modal-input" id="edit_parameter_group" type="number" name="group" />

			<label for="repetition">Repetition:</label>
			<input class="form-input modal-input" id="edit_parameter_repetition" type="number" name="repetition" />

			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_parameter_value" type="text" name="value" />

			<label for="description">Description:</label>
			<textarea id="edit_parameter_description" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="param_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
