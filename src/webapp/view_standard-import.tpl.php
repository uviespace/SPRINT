<h2>Project <?=$current_project[0]['name']?> - Standard <?=$current_standard[0]['name']?></h2>

<h2>Import to Standard</h2>

<?php if(count($import_msg) > 0): ?>
	<h4>Import messages:</h4>
	<div>
		<?php foreach($import_msg as $msg): ?>
			<div><?=$msg?></div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if (count($import_results) > 0): ?>
	<h4>Imported services and sub-services with their parameters</h4>

	<table class="table">
		<thead>
			<tr>
				<th>Service</th>
				<th>Sub-Service</th>
				<th>Parameter ID</th>
				<th>Parameter name</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($import_results as $i_r): ?>
				<tr>
					<td><?=$i_r['service']?></td>
					<td><?=$i_r['sub_service']?></td>
					<td><?=$i_r['param_id']?></td>
					<td><?=$i_r['param_name']?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>

<form style="width: 100%"
	  method="POST"
	  actions="view_standard.import.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">
	<div class="standard-import-container">
	
		
		<div class="standard-import-sidebar"><div>Select project:</div></div>
		<div class="standard-import-content">
			<select name="project" class="form-input-slim" onchange="form.submit()">
				<?php foreach($projects as $p): ?>
					<option value="<?=$p['id']?>" <?=$selected_project == $p['id'] ? 'selected' : ''?>><?=$p['name']?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="standard-import-sidebar"><div>Select standard:</div></div>
		<div class="standard-import-content">
			<select name="standard" class="form-input-slim" onchange="form.submit()">
					<?php foreach($standards as $s): ?>
						<option value="<?=$s['id']?>" <?=$selected_standard == $s['id'] ? 'selected' : ''?> ><?=$s['name']?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="standard-import-sidebar"><div>Header:</div></div>
		<div class="standard-import-content">
			<?php if ($selected_standard != -1): ?>
				<?php if($tc_header_cnt > 0): ?>
					<div>TC header already defined</div>
				<?php else: ?>
					<div><input type="checkbox"
								name="tc_header_check"
								value="1"
								<?=$tc_header_selected == 1 ? 'checked' : ''?> />
						<span>TC Header</span></div>
				<?php endif; ?>

				<?php if($tm_header_cnt > 0): ?>
					<div>TM header already defined</div>
				<?php else: ?>
					<div><input type="checkbox"
								name="tm_header_check"
								value="1"
								<?=$tm_header_selected == 1 ? 'checked' : ''?> />
						<span>TM Header</span></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="standard-import-sidebar"><div>Select service:</div></div>
		<div class="standard-import-content">
			<?php if (count($services) > 0): ?>
				<input type="checkbox" name="sel_all_services" value="1" onchange="form.submit()" />
				<span>Select all</span>
			<?php endif; ?>
			<?php foreach($services as $s): ?>
				<div>
					<input type="checkbox"
						   name="sel_services[]"
						   value="<?=$s['id']?>"
						   onchange="form.submit()"
					<?=$s['service_exists'] != null ? 'disabled' : ''?>
					<?=isset($_POST['sel_services']) && in_array($s['id'], $_POST['sel_services']) ? 'checked' : '' ?> />
					<span><?=$s['name']?> (Type: <?=$s['type']?>)</span>
					<?php if($s['service_exists'] != null): ?>
						<span style="color: red;">Service already exists</span>
					<?php endif; ?>
					
					<div>
						<?php if (isset($s['sub_services'])): ?>
							<?php foreach($s['sub_services'] as $ss): ?>
								<div class="standard-import-sub-service">
									<input type="checkbox" name="sel_sub_services[]", value="<?=$ss['id']?>" checked />
									<span>(<?=$ss['type']?>, <?=$ss['subtype']?>) <?=$ss['name']?></span>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>


		<?php if (count($services) > 0): ?>
			<div class="standard-import-sidebar"></div>
			<div class="standard-import-content">
				<input type="submit" class="btn" name="import" value="Import selected services" />
			</div>
		<?php endif; ?>
	</div>
</form>

