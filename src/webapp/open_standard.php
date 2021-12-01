<?php
session_start();
if(!isset($_SESSION['userid'])) {
    //die('Please <a href="login.php">login</a> first!');
    echo "Please <a href='login.php'>login</a> first!";
    echo "<br/><br/>";
    echo "<img src='img/loading.gif' />";
    header( "refresh:8;url=login.php" );
    die('');
}
require 'api/db_config.php';

if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
$project_name = "";
$standard_name = "";
$standard_desc = "";

$sql = "SELECT * FROM `project` WHERE `id` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $project_name = $row["name"];
    }
} else {
    //echo "0 results";
}

$sql = "SELECT * FROM `standard` WHERE `id` = ".$idStandard;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $standard_name = $row["name"];
        $standard_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

$idApplication = 0;

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

//Abfrage der Rolle des Users
$sql = "SELECT * FROM userproject WHERE idProject = ".$idProject." AND (idUser = ".$userid." OR email = '".$userEmail."')";
$result = $mysqli->query($sql);
$idRole = 5;
while ($row = $result->fetch_assoc()) {
    $idRoleRead = $row["idRole"];
    if ($idRoleRead < $idRole) { $idRole = $idRoleRead; };
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Standard <?php echo $standard_name;?> </title>
	<!-- https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css -->
	<link rel="stylesheet" type="text/css" href="ext/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.js -->
	<script type="text/javascript" src="ext/ajax/libs/jquery/3.1.0/jquery.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js -->
	<script src="ext/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css -->
	<link href="ext/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="int/layout.css">
    <script type="text/javascript" src="int/config.js"></script>
	<script type="text/javascript" src="js/item-ajax_view-project-standard.js"></script>
	<style type="text/css">

	</style>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h2>
		        </div>
				
				<!--
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
				-->
		    </div>
		</div>

		      <div class="modal-body">
					<!--<form data-toggle="validator" action="api/update_view-project-standard.php" method="put">

						<input type="hidden" name="id" class="edit-id">
						<input type="hidden" name="idProject" value="<?php echo $idProject; ?>">
						<input type="hidden" name="idStandard" value="<?php echo $idStandard; ?>">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." value="<?php echo $standard_name; ?>" required />
							<div class="help-block with-errors"></div>
						</div>
 
 						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" data-error="Please enter description."><?php echo $standard_desc; ?></textarea>
							<div class="help-block with-errors"></div>
						</div>
 
						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Save</button>
						</div>

		      		</form>-->
					
					
					<form data-toggle="validator" action="api/update_view-project-standard.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." value="<?php echo $standard_name; ?>" readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" data-error="Please enter description." rows="2" readonly ><?php echo $standard_desc; ?></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<!--<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Submit</button>
						</div>-->

		      		</form>

		      </div>
			  



<?php

/*
$sql = "SELECT * FROM `application` WHERE idProject = $idProject";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Applications</h3> $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_application.php?idProject$id=&idApplication=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}

$sql = "SELECT * FROM `standard` WHERE idProject = $idProject";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Standards</h3> $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_standard.php?idProject=$id&idStandard=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}
*/

?>

<?php if ($idRole < 3) { ?>
				<div>
					<a href="view_standard-import.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>"><button style="width:180px;">Import to Standard ...</button></a>
				</div>
<?php } else { ?>
				<div>
					<button style="width:180px;color:gray;" disabled>Import to Standard ...</button>
				</div>
<?php } ?>

				<br/>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_tcheader.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">TC Header...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_tmheader.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">TM Header...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_apid.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">APIDs...</a>
				</div>
				
                <br/>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_service.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Services...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_packet.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Packets...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="sel_packet-derived.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Packets (Derived Packets)</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="sel_packet-params.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Packets (Parameters)</a>
				</div>

                <br/>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_type.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Datatypes...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="sel_type-enumeration.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Datatypes (Enumerations)</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_constant.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Constants...</a>
				</div>

                <br/>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_parameter.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Parameters...</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="sel_parameter-derived.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Parameters (Derived Packets)</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="sel_parameter-limit.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Parameters (Limits)</a>
				</div>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="view_datapool.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Datapool...</a>
				</div>

                <br/>

				<div style="background-color:#EEEEEE;padding:2px;">
					<a href="open_standard_editor.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">Settings...</a>
				</div>

				<br/><br/>

				<hr>

				<div>
					<a href="sel_type-SCOS2000.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>">SCOS2000 Test</a>
				</div>

				<hr>

				<br/><br/>

				<div class="topcorner_left">
					<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
					<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
					<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="open_project.php?id=<?php echo $idProject?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>