<h2>Packets</h2>

<button id="create_packet_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Packet
</button>

<table id="table_packet" class="table">
	<thead>
		<th>ID</th>
		<th>Kind</th>
		<th>Type</th>
		<th>Subtype</th>
		<th>Dscriminant</th>
		<th>Domain</th>
		<th style="text-align:left;padding-left:2vw;">Name</th>
		<th style="text-align:left;padding-left:2vw;">Short Desc.</th>
		<th>Action</th>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_packet_row">
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align:left;padding-left:2vw;"></td>
		<td style="text-align:left;padding-left:2vw;"></td>
		<td>
			<div class="btn-group">
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
			<h3>Edit Packet</h3>
		</div>

		<div class="modal-body">
			<label for="apid">Process/APID:</label>
			<select name="apid" id="edit_packet_apid" class="form-input modal-input">
				<?php foreach($apids as $apid): ?>
					<option value="<?=$apid["id"]?>"><?=$apid["address"]?> / <?=$apid["name"]?> (<?=$apid["id"]?>)</option>
				<?php endforeach; ?>
			</select>

			<label for="kind">Kind:</label>
			<select name="kind" id="edit_packet_kind" class="form-input modal-input">
				<option value="0">TC</option>
				<option value="1">TM</option>
			</select>

			<label for="type">Type:</label>
			<select name="type" id="edit_packet_type" class="form-input modal-input">
				<?php foreach($services as $service): ?>
					<option value="<?=$service["type"]?>"><?=$service["type"]?> (<?=$service["name"]?>) </option>
				<?php endforeach; ?>
			</select>

			<label for="subtype">Subtype:</label>
			<input id="edit_packet_subtype" class="form-input modal-input" type="number" name="subtype" />

			<label for="Domain">Domain:</label>
			<input id="edit_packet_domain" class="form-input modal-input" type="text" name="domain" />

			<label for="name">Name:</label>
			<input id="edit_packet_name" class="form-input modal-input" type="text" name="name" />

			<label for="short_desc">Short Desc.:</label>
			<input id="edit_packet_short_desc" class="form-input modal-input" type="text" name="short_desc" />

			<label for="desc">Description:</label>
			<textarea id="edit_packet_desc" rows="4" class="modal-input" name="desc"></textarea>

			<label for="param_desc">Parameter Desc.:</label>
			<input id="edit_packet_param_desc" class="form-input modal-input" type="text" name="param_desc" />

			<label for="dest_desc">Destination Desc.:</label>
			<input id="edit_packet_dest_desc" class="form-input modal-input" type="text" name="desc_desc" />

			<label for="code">Code:</label>
			<input id="edit_packet_code" class="form-input modal-input" type="text" name="code" />
		</div>

		<div class="modal-footer">
			<button id="packet_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>
