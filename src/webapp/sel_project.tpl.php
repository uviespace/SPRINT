<h2>Projects</h2>


<form method="post" enctype="multipart/form-data">
	<div class="user-card">
		<input type="file" id="file-upload" name="importfile[]" multiple="multiple" style="display:none" />
		<label for="file-upload" class="btn">Choose File</label>
		<input class="btn" type="submit" name="import" value="Import" />
	</div>
</form>


<div class="project-table">
	<?php foreach($projects as $project):  ?>
	<div class="project-row">
		<a href="open_project.php?id=<?=$project['id']?>"><?=$project['id']?> <strong><?=$project['name']?></strong></a>
		<?php if ($project["isPublic"]): ?>
		<span class="project-icon"><img src="img/isPublic20.png"></span>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>
