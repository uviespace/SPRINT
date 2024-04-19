<h4> Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Calibration Curves</h2>


<a class="btn" href="open_calibration_editor.php?idProject=<?=$_GET["idProject"]?>&idStandard=<?=$_GET["idStandard"]?>">
	Create new calibration curve
</a>


<?php foreach($cal_curves as $curve): ?>
	<div class="curve-list-item" style="background: <?=curve_list_background($curve["param_count"])?>;">
		<span style="width: 8%;"><?=$curve["id"]?></span>
		<span style="width: 20%;">
			<a href="open_calibration_editor.php?id=<?=$curve["id"]?>&idProject=<?=$_GET["idProject"]?>&idStandard=<?=$_GET["idStandard"]?>">
				<?=$curve["name"]?>
			</a>
		</span>
		<span style="width: 50%;">(<?=$curve["shortDesc"]?>)</span>
		<span style="float: right;">
			<span><?=$curve["param_count"]?></span>
			<span style="padding:1px 4px; background: <?=curve_type_color($curve["type"])?>;"><?=$curve["type_name"]?></span>
		</span>
	</div>
<?php endforeach; ?>



<h2>Parameters</h2>

<?php foreach($params as $param): ?>
	<div class="param-curve-list-item" style="background: #f1f1f1">
		<span style="width: 8%;"><?=$param["id"]?></span>
		<span style="width: 40%;">
			<?=$param["domain"]?> / <?=$param["name"]?>
		</span>
		<span style="float:right;">
			<select id="<?=$param["id"]?>"  name="curve" class="form-input-slim"
					style="margin: -5px;" value="<?=$curve["id"]?>" onchange="set_calibration_curve(<?=$param["id"]?>)" >
				<option value="0">No curve</option>
				<?php foreach($select_curves as $curve): ?>
					<?php if ($curve["id"] == $param["curve_id"]): ?>
						<option value="<?=$curve["id"]?>" selected><?=$curve["id"]?> <?=$curve["name"]?> (<?=$curve["type_name"]?>)</option>
					<?php else: ?>
						<option value="<?=$curve["id"]?>"><?=$curve["id"]?> <?=$curve["name"]?> (<?=$curve["type_name"]?>)</option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</span>
	</div>
<?php endforeach; ?>



<?php
function curve_list_background($count)
{
	if ($count == 0)
		return "#f1f1f1";
	else
		return "lightgreen";
}

function curve_type_color($type)
{
	if ($type == 0)
		return "yellow";
	else if ($type == 1)
		return "orange";
	else if ($type == 2)
		return "#C4A484";
	else
		return "red";
}
?>
