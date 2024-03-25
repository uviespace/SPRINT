<h4>Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Limits</h2>


<div class="packet-table">
	<?php foreach($parameters as $param): ?>
		<div class="packet-row">
			<div class="col-open">
				<button class="btn btn-small" onclick="toggle_limit_visibility(<?=$param["id"]?>)">
					<i id="icon-<?=$param["id"]?>" class="nf nf-cod-triangle_right"></i>
				</button>
			</div>

			<div class="col-id"><?=$param["id"]?></div>
			<div class="col-type"><?=$param["domain"]?> / <?=$param["name"]?></div>
			<div class="col-param">
				<?php if ($param["limit_count"] > 0): ?>
					<i class="nf nf-fa-check_circle success"></i>
				<?php else: ?>
					<i class="nf nf-oct-x_circle_fill error"></i>
				<?php endif; ?>
			</div>

			<div id="limit-<?=$param["id"]?>" class="packet-container" style="display: none;">
				<button id="create_limit_button_id_<?=$param["id"]?>" class="btn">
					<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px"></i>Add Limit
				</button>

				<table id="table_limit_<?=$param["id"]?>" class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Type</th>
							<th>Lower Value</th>
							<th>Higher Value</th>
							<th>Setting</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			
		</div>
	<?php endforeach; ?>
</div> 



<template id="table_limit_row">
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


<div id="limit_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Limit</h3>
		</div>

		<div class="modal-body">
			<label for="type">Type:</label>
			<input class="form-input modal-input" id="edit_limit_type" type="number" name="type" />

			<label for="lower_value">Lower Value:</label>
			<input class="form-input modal-input" id="edit_limit_lower_value" type="text" name="lower_value" />

			<label for="upper_value">Upper Value:</label>
			<input class="form-input modal-input" id="edit_limit_upper_value" type="text" name="upper_value" />

			<label for="setting">Setting:</label>
			<textarea id="edit_limit_setting" rows="14" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="limit_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div> 
