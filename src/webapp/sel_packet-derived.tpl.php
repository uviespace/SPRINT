<h4> Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Derived Packets</h2>

<div class="packet-table">
	<?php foreach($packets as $packet): ?>
		<div class="packet-row">
			<div class="col-open">
				<button class="btn btn-small" onclick="toggle_derivation_visibility(<?=$packet["id"]?>)">
					<i id="icon-<?=$packet["id"]?>" class="nf nf-cod-triangle_right"></i>
				</button>
			</div>

			<div class="col-id"><?=$packet["id"]?></div>
			<div class="col-type"><?=$packet["domain"]?> / <?=$packet["kind"]?>(<?=$packet["type"]?>/<?=$packet["subtype"]?>)</div>
			<div class="col-name"><?=$packet["name"]?></div>
			<div class="col-param"><?=$packet["packet_count"]?> packets(s)</div>

			<div id="packet-<?=$packet["id"]?>" class="packet-container" style="display: none;">
				<button id="create_derivation_button_id_<?=$packet["id"]?>" class="btn">
					<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px"></i>Add Derivation
				</button>

				<table id="table_packet_<?=$packet["id"]?>" class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Discriminant</th>
							<th>Name</th>
							<th>Short Desc.</th>
							<th>Description</th>
							<th>Parameter Desc.</th>
							<th>Destination Desc.</th>
							<th>Code</th>
							<th># Param.</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>

				
			</div>
		</div>
	<?php endforeach; ?>
</div>


<template id="table_packet_row">
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
				<button><i class="nf nf-md-folder_open_outline"></i></button>
				<button><i class="nf nf-cod-edit"></i></button>
				<button><i class="nf nf-md-delete_outline"></i></button>
			</div>
		</td>
	</tr>
</template>


<div id="packet_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Edit Derived Packet</h3>
		</div>

		<div class="modal-body">
			<label for="discriminant">Discriminant</label>
			<select name="dscriminant" id="edit_packet_discriminant" class="form-input modal-input" data-filter="on">
				<?php foreach($discriminants as $disc): ?>
					<option value="<?=$disc["name"]?>"><?=$disc["idType"]?> / <?=$disc["name"]?></option>
				<?php endforeach; ?>
			</select>

			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_packet_name" type="text" name="name" />

			<label for="short_desc">Short Desc.:</label>
			<input class="form-input modal-input" id="edit_packet_short_desc" type="text" name="short_desc" />
	
			<label for="description">Description</label>
			<textarea id="edit_packet_description" rows="4" class="modal-input"></textarea>
	
			<label for="param_desc">Parameter Desc.:</label>
			<input class="form-input modal-input" id="edit_packet_param_desc" type="text" name="param_desc" />

			<label for="destination_desc">Destination Desc.:</label>
			<input class="form-input modal-input" id="edit_packet_destination_desc" type="text" name="destination_desc" />

			<label for="code">Code:</label>
			<input class="form-input modal-input" id="edit_packet_code" type="test" name="code" />
		</div>

		<div class="modal-footer">
			<button id="packet_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>


<?php if (isset($_GET["open"])): ?>
	<script>
	 window.onload = toggle_derivation_visibility(<?=$_GET["open"]?>);
	 setTimeout(function() {
		 document.getElementById('icon-<?=$_GET["open"]?>')
				 .scrollIntoView({ behavior: "smooth" });
	 }, 700);
	</script>
<?php endif; ?>
