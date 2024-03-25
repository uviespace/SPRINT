<h2>Project <?=$project[0]["name"]?> - Standard <?=$standard[0]["name"]?></h2>

<table class="name-value-table">
	<tbody>
		<tr>
			<td>Name</td>
			<td><?=$standard[0]["name"]?></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><?=$standard[0]["desc"]?></td>
		</tr>
	</tbody>
</table>


<?php if($id_role < 4): ?>
	<form method="post" enctype="multipart/form-data" style="margin-bottom: 32px;">
		<input type="submit" name="export" value="Export" class="btn">
		<a class="btn" href="view_standard-import.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Import to Standard...</a>

		<?php if (!empty($errors)): ?>
			<?php foreach($errors as $e): ?>
				<div style="color: red;"><?= $e ?></div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (!empty($message)): ?>
			<div style="color: green;"><?= $message ?></div>
		<?php endif; ?>		
	</form>
	
<?php endif; ?>


<div class="sprint-card sprint-card-wide">
	<div>
		<a href="view_tcheader.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">TC Header... </a>
		<span class="sprint-card-badge"><?=$tc_header_cnt?></span>
	</div>
	<div>
		<a href="view_tmheader.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">TM Header... </a>
		<span class="sprint-card-badge"><?=$tm_header_cnt?></span>
	</div>
	<div>
		<a href="view_apid.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">APIDs... </a>
		<span class="sprint-card-badge"><?=$apid_cnt[0]["cnt"]?></span>
	</div>
</div>

<div class="sprint-card sprint-card-wide">
	<div>
		<a href="view_service.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Services...</a>
		<span class="sprint-card-badge"><?=$services_cnt[0]["cnt"]?></span>
	</div>

	<div>
		<a href="view_packet.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Packets...</a>
		<span class="sprint-card-badge"><?=$packets_cnt[0]["cnt"]?></span>
	</div>

	<div>
		<span class="h-space-5"></span>
		==> <a href="sel_packet-derived.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Packets (Derived Packets)</a>
		<span class="sprint-card-badge"><?=$derived_packets_cnt[0]["cnt"]?> derived | <?=$base_packets_cnt[0]["cnt"]?> base</span>
	</div>

	<div>
		<span class="h-space-5"></span>
		==> <a href="sel_packet-params.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Packets (Parameters)</a>
		<span class="sprint-card-badge"><?=$params_packets_cnt[0]["cnt"]?></span>
	</div>
</div>

<div class="sprint-card sprint-card-wide">
	<div>
		<a href="view_constant.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Constants...</a>
		<span class="sprint-card-badge"><?=$consts_cnt[0]["cnt"]?></span>
	</div>

	<div>
		<a href="view_type.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Datatypes...</a>
		<span class="sprint-card-badge"><?=$types_cnt[0]["cnt"]?></span>
	</div>

	<div>
		<span class="h-space-5"></span>
		==> <a href="sel_type-enumeration.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Datatypes (Enumerations)</a>
		<span class="sprint-card-badge"><?=$json_enum_cnt[0]["cnt"]?> JSON | <?=$db_enum_cnt[0]["cnt"]?> DB</span>
	</div>
</div>

<div class="sprint-card sprint-card-wide">
	<div>
		<a href="view_datapool.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Datapool...</a>
		<span class="sprint-card-badge"><?=$datapool_cnt[0]["cnt"]?></span>
	</div>

	<div>
		<a href="view_parameter.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Parameters...</a>
		<span class="sprint-card-badge"><?=$params_cnt[0]["cnt"]?></span>
	</div>

	<!--<div>
		<span class="h-space-5"></span>
		==> <a href="sel_parameter-derived.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Parameters (Derived Packets)</a>
		<span class="sprint-card-badge"><?=$derived_packets_cnt[0]["cnt"]?></span>
	</div>-->

	<div>
		<span class="h-space-5"></span>
		==> <a href="sel_parameter-limit.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Parameters (Limits)</a>
		<span class="sprint-card-badge"><?=$limits_cnt[0]["cnt"]?> limits | <?=$limits_cnt[1]["cnt"]?> no limits</span>
	</div>

	<div>
		<span class="h-space-5"></span>
		==> <a href="sel_parameter-calibration.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Parameters (Calibration Curves)</a>
		<span class="sprint-card-badge">
			<?=$params_calib[0]["cnt"]?> with curves | <?=$params_no_calib[0]["cnt"]?> without curves | <?=$calib[0]["cnt"]?> calibration curves
		</span>
	</div>
</div>

<div class="sprint-card sprint-card-wide">
	<div>
		<a href="open_standard_editor.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Settings...</a>
	</div>
	<div>
		<a href="open_standard_check.php?idProject=<?=$_GET['idProject']?>&idStandard=<?=$_GET['idStandard']?>">Check Standard...</a>
	</div>
</div>
