<h2><?=$pagetitle?></h2>

<div id="settings_form" style="width: 600px;"></div>
<button class="btn" onclick="save_settings()">
	<i class="nf nf-fa-save" style="margin-right: 4px; font-size: 16px;" ></i>Save Settings
</button>


<script>
 var component_schema = <?=$component[0]["componentSettings"]?>;
 var component_settings = <?=$component_settings[0]["setting"]?>;
</script>

