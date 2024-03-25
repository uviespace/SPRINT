<h4>Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Enumerations</h2>

<div class="packet-table">
	<?php foreach($enums as $enum): ?>
		<div class="packet-row">
			<div class="col-open">
				<button class="btn btn-small" onclick="toggle_enum_visibility(<?=$enum["id"]?>)">
					<i id="icon-<?=$enum["id"]?>" class="nf nf-cod-triangle_right"></i>
				</button>
			</div>

			<div class="col-id"><?=$enum["id"]?></div>
			<div class="col-type"><?=$enum["domain"]?> / <?=$enum["name"]?></div>
			<div class="col-param"><?=$enum["enum_count"]?> enum items</div>

			<div id="enum-<?=$enum["id"]?>" class="packet-container" style="display: none;">
				<button id="create_enum_button_id_<?=$enum["id"]?>" class="btn">
					<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px"></i>Add Enum Item
				</button>

				<table id="table_enum_<?=$enum["id"]?>" class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Value</th>
							<th>Description</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>
</div>



<template id="table_enum_row">
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

<div id="enum_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Enum</h3>
		</div>

		<div class="modal-body">
			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_enum_name" type="text" name="name" />

			<label for="value">Value:</label>
			<input class="form-input modal-input" id="edit_enum_value" type="number" name="value" />

			<label for="description">Description:</label>
			<textarea id="edit_enum_description" rows="4" class="modal-input"></textarea>
		</div>

		<div class="modal-footer">
			<button id="enum_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div> 
