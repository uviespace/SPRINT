<h2>Project <?=$project[0]["name"]?> - Application <?=$application[0]["name"]?></h2>

<table class="name-value-table">
	<tbody>
		<tr>
			<td>Name</td>
			<td><?=$application[0]["name"]?></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><?=$application[0]["desc"]?></td>
		</tr>
	</tbody>
</table>


<form method="POST" action="open_application.php?idProject=<?=$_GET["idProject"]?>&idApplication=<?=$_GET["idApplication"]?>">
	<div class="item-box">
		<div class="item-box-header">Relations</div>
		<input type="hidden" name="idProject" value="<?=$_GET['idProject']?>" />
		<input type="hidden" name="idApplication" value="<?=$_GET['idApplication']?>" />
		<?php foreach($standards as $idx => $std): ?>
			<div class="item-box-item">
				<input type="hidden" name="idStandard[]" id="<?=$idx?>" value="<?=$std["id"]?>" />
				<div class="item-box-name">
					<a href="view_application-packet.php?idProject=<?=$_GET['idProject']?>&idApplication=<?=$_GET['idApplication']?>&idStandard=<?=$std["id"]?>"><?=$std["id"]?> <?=$std["name"]?></a>
				</div>

				<div class="item-box-control">
					<select name="relation_type" class="form-input-slim" style="display: inline-block">
						<?php
						if (array_key_exists($std["id"], $rel_map)) {
							$relation = $rel_map[$std["id"]]["relation"];
						} else {
							$relation = -1;
						}
						?>
						<option value="0" <?=$relation == 0 ? 'selected' : ''?>>service user</option>
						<option value="1" <?=$relation == 1 ? 'selected' : ''?>>service provider</option>
					</select>

					<?php if (array_key_exists($std["id"], $rel_map)): ?>
						<button type="submit" class="btn btn-semi-small" name="chg_relation" value="<?=$idx?>">Change</button>
						<button type="submit" class="btn btn-semi-small btn-danger" name="del_relation" value="<?=$idx?>"
								onclick="return confirm('Are you sure you want to delete this component?')">Delete</button>
					<?php else: ?>
						<button type="submit" class="btn btn-semi-small" name="add_relation" value="<?=$idx?>">Add</button>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</form>


<form method="POST" action="open_application.php?idProject=<?=$_GET["idProject"]?>&idApplication=<?=$_GET["idApplication"]?>">
	<div class="item-box">
		<div class="item-box-header">Components</div>
		<input type="hidden" name="idProject" value="<?=$_GET['idProject']?>" />
		<input type="hidden" name="idApplication" value="<?=$_GET['idApplication']?>" />

		<?php foreach($components as $idx => $comp): ?>
			<div class="item-box-item">
				<input type="hidden" name="idComponent[]" id="<?=$idx?>" value="<?=$comp["id"]?>" />
				<div class="item-box_name">
					<?=$comp["id"]?> <?=$comp["name"]?>
				</div>

				<div class="item-box-control">
					<?php if(is_null($comp["active"])): ?>
						<button type="submit" class="btn btn-semi-small" name="add_component" value="<?=$idx?>">Add</button>
					<?php elseif($comp["active"] == 1): ?>
						<button type="submit" class="btn btn-semi-small" name="deact_component" value="<?=$idx?>">Deactivate</button>
					<?php elseif($comp["active"] == 0): ?>
						<button type="submit" class="btn btn-semi-small" name="del_component" value="<?=$idx?>">Delete</button>
						<button type="submit" class="btn btn-semi-small" name="act_component" value="<?=$idx?>">Activate</button>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
			
	</div>
</form>


<?php if($id_role < 4): ?>
<div class="tab-container">
	<input id="tab_1" type="radio" name="tabs" class="tab-input" checked />
	<label for="tab_1" class="tab-label">Build</label>

	<input id="tab_2" type="radio" name="tabs" class="tab-input" />
	<label for="tab_2" class="tab-label">Import Acronym List</label>

	<input id="tab_3" type="radio" name="tabs" class="tab-input" />
	<label for="tab_3" class="tab-label">Import Data Pool List</label>

	<input id="tab_4" type="radio" name="tabs" class="tab-input" />
	<label for="tab_4" class="tab-label">Import Requirement List</label>

	<input id="tab_5" type="radio" name="tabs" class="tab-input" />
	<label for="tab_5" class="tab-label">Import Enumeration List</label>

	<input id="tab_6" type="radio" name="tabs" class="tab-input" />
	<label for="tab_6" class="tab-label">Import Calib. Curve</label>

	<section id="content_1" class="tab_content">
		<form id="form_build_dp_csv" method="post">
			<input type="submit" name="build_dp_list" value="Build Data Pool CSV" class="btn" />
			<div>Progress bar</div>
			<textarea class="form-input modal-input" readonly><?=$message_dp_list?></textarea>
		</form>

		<div style="margin-top: 72px"></div>
		
		<form id="form_build" method="post">
			<input type="submit" name="build" value="Build" class="btn" />
			<div>Progress bar</div>
			<textarea rows=25" class="form-input modal-input" readonly><?=$message_build_app?></textarea>
		</form>
	</section>

	<section id="content_2" class="tab_content">
		Acronym List was never implementet and will be done at a later date.
	</section>

	<section id="content_3" class="tab_content">
		Datapool List import was never implementet and will be done at a later date.
	</section>

	<section id="content_4" class="tab_content">
		Requirement List import was never implementet and will be done at a later date.
	</section>

	<section id="content_5" class="tab_content">
		Enumeration List import was never implementet and will be done at a later date.
	</section>

	<section id="content_6" class="tab_content">
		<form id="form_import_calib_curve" method="post" enctype="multipart/form-data">
			<div class="form-container">
				<div class="form-row">
					<div class="form-label">Calibration Curve Type:</div>
					<div class="form-value">
						<select id="cal_curve_type" name="cal_curve_type" class="form-input" onchange="update_form()">
							<option value="1">Numerical</option>
							<option value="2">Polynomial</option>
							<option value="3">Logarithmical</option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-label">Action:</div>
					<div class="form-value">
						<select id="cal_curve_action" name="cal_curve_action" class="form-input" onchange="update_form()">
							<option value="1">Add new calibration</option>
							<option value="2">Add new points</option>
							<option value="3">Replace calibration</option>
						</select> 
					</div>
				</div>

				<div class="form-row">
					<div class="form-label">Standard:</div>
					<div class="form-value">
						<select id="cal_curve_standard" class="form-input" name="cal_curve_standard" onchange="update_form()">
							<?php foreach($standards as $std): ?>
								<option value="<?=$std["id"]?>"><?=$std["name"]?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div id="cal_curve_existing_row" class="form-row" style="display: none;">
					<div class="form-label">Existing Calibration Curve:</div>
					<div class="form-value">
						<select id="cal_curve_existing" class="form-input" name="cal_curve_existing">
						</select>
					</div>
				</div>

				<hr class="form-separator" />

				<div id="cal_curve_settings_block">
					<div class="form-row">
						<div class="form-label">Name:</div>
						<div class="form-value">
							<input class="form-input" name="cal_curve_name" type="text" />
						</div>
					</div>

					<div class="form-row">
						<div class="form-label">Short Description:</div>
						<div class="form-value">
							<input class="form-input" name="cal_curve_short_desc" type="text" />
						</div>
					</div>
				
					<div id="cal_curve_numerical_settings_additions">
						<hr class="form-separator" />
						<div class="form-row">
							<div class="form-label">Engineering Format:</div>
							<div class="form-value">
								<select class="form-input" name="cal_curve_eng_format">
									<option value="I">Signed Integer (I)</option>
									<option value="U">Unsigned Integer (U)</option>
									<option value="R">Real (R)</option>
								</select>
							</div>
						</div>

						<div class="form-row">
							<div class="form-label">Raw Format:</div>
							<div class="form-value">
								<select class="form-input" name="cal_curve_raw_format">
									<option value="I">Signed Integer (I)</option>
									<option value="U">Unsigned Integer (U)</option>
									<option value="R">Real (R)</option>
								</select>
							</div>
						</div>

						<div class="form-row">
							<div class="form-label">Radix:</div>
							<div class="form-value">
								<select class="form-input" name="cal_curve_radix">
									<option value="D">Decimal (D)</option>
									<option value="H">Hexadecimal (H)</option>
									<option value="O">Octal (O)</option>
								</select>
							</div>
						</div>

						<div class="form-row">
							<div class="form-label">Unit:</div>
							<div class="form-value"><input class="form-input" name="cal_curve_unit" type="text" /></div>
						</div>

						<div class="form-row">
							<div class="form-label">Interpolation:</div>
							<div class="form-value">
								<select class="form-input" name="cal_curve_interpolation">
									<option value="P">Interpolate/Extrapolate</option>
									<option value="F">Disabled</option>
								</select>
							</div>
						</div>
					</div>
					<hr class="form-separator" />
				</div>

				<div class="form-row">
					<div class="form-label">Select file:</div>
					<div class="form-value">
						<input class="form-input" type="file" name="cal_curve_file" required />
					</div>
				</div>

				<div class="form-row">
					<div class="form-label"></div>
					<input class="btn" type="submit" name="cal_curve_submit" value="Import Calib. Curve" />
				</div>

				<div class="form-row">
					<div class="form-label"></div>
					<div class="form-value">
						<textarea rows="10" class="form-input" readonly><?=$calibration_import_msg?></textarea>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?php endif; ?>
