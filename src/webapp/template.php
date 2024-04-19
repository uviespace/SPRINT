<!DOCTYPE html>
<html>
    <head>
		<title><?=$pagetitle?></title>

		<link rel="stylesheet" type="text/css" href="ext/iziToast/iziToast.min.css">
		
		<link rel="stylesheet" type="text/css" href="layout/layout.css">
		<?php if (isset($site_css)): ?>
			<link rel="stylesheet" type="text/css" href="<?=$site_css?>">
		<?php endif; ?>

		<script type="text/javascript" src="ext/iziToast/iziToast.min.js"></script>
		<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

		<script type="text/javascript" src="int/config.js"></script>
		<script type="text/javascript" src="js/global.js"></script>

		<?php if (isset($site_js)): ?>
			<script type="text/javascript" src="<?=$site_js?>"></script>
		<?php endif; ?>

    </head>

    <body>
		<div class="content-container">
			<div class="sidebar">
				<div class="sprint-logo-header">
					<img src="img/sprint_1_64x64.png" alt="Logo SPRINT" width="45">
					<span>SPRINT</span>
				</div>

				<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="180" style="padding: 5px;"><br/>
				<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="180" style="padding: 5px;">
				

				<?php //Here will be  additional content  ?>

				
				<?php if (isset($_SESSION['username'])): ?>
					<div class="user-block">
						<div>You are logged in as:</div>
						<div style="font-weight: bold;"><?= $_SESSION['username'] ?></div>
						<a class="btn btn-sidebar" href="profile.php">Edit Profile</a>
						<a class="btn btn-sidebar" href="logout.php">Logout</a>
					</div>
				<?php endif; ?>


				<div class="action-block">
					<?php if (isset($sidebar_actions)): ?>
						<?php foreach($sidebar_actions as $action): ?>
							<a class="btn btn-action" href="<?= $action["link"]; ?>"><?= $action["label"]; ?></a>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				
				
			</div>

			
			<div class="content">
				<div class="top-header">
					<h2>Space Project Resource Integration Network Toolbox</h2>
				</div>
				
				<?php include $tpl  ?>

				<div class="footer">
					<div style="float: right;">Ⓒ 2019-<?php echo date("Y"); ?>, University of Vienna</div>
				</div>
			</div>

			
		</div>
    </body> 
</html>
