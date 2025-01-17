<h2>Project <?= $project_name ?></h2>

<?php if ($id_role < 4): ?>
	<form method="post" enctype="multipart/form-data">
		<div class="import-export-container">
			<div class="export-area">
				<div class="import-export-header">Export</div>
				<div class="import-export-content">
					<input type="submit" class="btn btn-primary" name="export" value="Export Project" />
				</div>
			</div>
			<div class="import-area">
				<div class="import-export-header">Import</div>
				<div class="import-export-content">
					<input class="form-input" type="file" name="importfile[]" multiple="multiple" />
					<input type="submit" class="btn btn-primary" name="import" value="Import Standard" />
				</div>
			</div>
		</div>
		<div>
			<?php foreach($errors as $e): ?>
				<div style="color: red;"><?=$e?></div>
			<?php endforeach; ?>
		</div>
	</form>
<?php endif; ?>


<h3>Applications</h3>

<button id="create_application_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Application
</button>

<table id="table_applications" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Description</th>
			<th style="width: 100px;">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<template id="table_applications_row">
	<tr>
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


<div id="applications_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Application</h3>
		</div>
		<div class="modal-body">
			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_application_name"  type="text" name="name" />

			<label for="description">Description:</label>
			<textarea id="edit_application_description" rows="4" class="modal-input"></textarea> 
		</div>

		<div class="modal-footer">
			<button id="application_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>

<h3>Standards</h3>


<button id="create_standard_button_id" class="btn">
	<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Create Standard
</button>

<table id="table_standards" class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Description</th>
			<th style="width: 100px;">Action</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>

<template id="table_standards_row">
	<tr>
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

<div id="standards_modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="modal-close">&times</span>
			<h3>Standard</h3>
		</div>

		<div class="modal-body">
			<label for="name">Name:</label>
			<input class="form-input modal-input" id="edit_standard_name"  type="text" name="name" />

			<label for="description">Description:</label>
			<textarea id="edit_standard_description" rows="4" class="modal-input"></textarea> 
		</div>

		<div class="modal-footer">
			<button id="standards_submit_button" class="btn-submit"></button>
		</div>
	</div>
</div>


<h3>Document Management</h3>

<div class="button-grid">
	<a class="btn btn-disabled" href="sel_project-documentation.php?idProject=<?=$_GET['id']?>">Manage Documents...</a>
</div>

<div class="button-grid">
	<div class="sub-grid">
		<a class="btn btn-disabled" href="view_project-acronyms.php?idProject=<?=$_GET['id']?>">Manage Acronyms...</a>
		<a href="open_project?idProject=<?=$_GET['id']?>&action=exp_acr">
			<img src="img/download.png" width="25px" />
			<span class="badge"><?=$acronym_cnt?></span>
		</a>
	</div>
	<div class="sub-grid">
		<a class="btn btn-disabled" href="view_project-references.php?idProject=<?=$_GET['id']?>">Manage References...</a>
		<a href="open_project?idProject=<?=$_GET['id']?>&action=exp_ref" />
			<img src="img/download.png" width="25px" />
			<span class="badge"><?=$document_cnt?></span>
		</a>
	</div>
	<a class="btn btn-disabled" href="view_project-organisations.php?idProject=<?=$_GET['id']?>">Manage Organisations</a>
</div>

<h3>Requirement Management</h3>

<div class="button-grid">
	<a class="btn btn-disabled" href="sel_project-requirement.php?idProject=<?=$_GET['id']?>">Tailoring and Traceability</a>
</div>


<div class="button-grid">
	<a class="btn btn-disabled" href="view_project-requirements-external.php?idProject=<?=$_GET['id']?>">Manage Ext. Requ.S...</a>
	<a class="btn btn-disabled" href="view_project-requirements-external-requ.php?idProject=<?=$_GET['id']?>">Manage Ext. RequR...</a>
</div>


<div class="button-grid">
	<a class="btn btn-disabled" href="view_project-requirements-internal.php?idProject=<?=$_GET['id']?>">Manage Int. Requ.S ...</a>
	<div class="sub-grid">
		<a class="btn btn-disabled" href="view_project-requirements-internal-requ.php?idProject=<?=$_GET['id']?>">Manage Int. Requ.R ...</a>
		<a href="open_project.php?id=<?=$_GET['id']?>&action=exp_int_req">
			<img src="img/download.png" width="25px" />
			<span class="badge"><?=$int_req_cnt?></span>
		</a>
	</div>
</div>

<h3>Contributors / Users</h3>

<div class="sprint-card">
	<?php foreach($contributors as $contrib): ?>
		<div style="padding: 5px;">
			<?=$contrib['id']?> <strong><?=$contrib["email"]?></strong>
			<?php if ($contrib['idRole'] == 4): ?>
				<span style="float: right;"><img src="img/guest.png"></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<?php if ($id_role < 4): ?>
<a class="btn" href="view_project-contributor.php?idProject=<?=$_GET['id']?>">Manage Contributors ...</a>
<?php endif; ?>

<h3>Owner</h3>

<div class="sprint-card">
	<div style="padding: 5px;">
		<?=$owner[0]['id']?> <strong><?=$owner[0]['name']?></strong> (<?=$owner[0]['email']?>)
	</div>
</div>

