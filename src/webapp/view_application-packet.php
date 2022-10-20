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
require 'int/global_functions.php';

if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["rel"])) { $rel  = $_GET["rel"]; } else { $rel=2; };
$project_name = "";
$application_name = "";
$standard_name = "";
$standard_desc = "";

if ($rel == 0) {
    $rel_name = "Service User";
} else if ($rel == 1) {
    $rel_name = "Service Provider";
} else {
    $rel_name = "N/A";
}

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

$sql = "SELECT * FROM `application` WHERE `id` = ".$idApplication;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $application_name = $row["name"];
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

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

$idRole = get_max_access_level($mysqli, $idProject, $userid, $userEmail);
?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Application Services</title>
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
	<!-- https://github.com/knownasilya/jquery-highlight -->
	<script type="text/javascript" src="ext/jquery.highlite.js"></script>
	<link rel="stylesheet" type="text/css" href="int/layout.css">
    <script type="text/javascript" src="int/config.js"></script>
	<script type="text/javascript" src="int/livesearch.js"></script>
	<script type="text/javascript" src="js/item-ajax_view-application-packet.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Application <?php echo $application_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Packets for Application <?php echo $application_name;?> [<?php echo $rel_name; ?>]</h2>
		        </div>
                <?php if ($idRole < 4) { ?>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Add Item
				</button>
		        </div>
                <?php } ?>
		    </div>
		</div>

		<ul id="pagination" class="pagination-sm"></ul>

		<div class="result_nmb_rows">
			<input id="result_nmb" class="result_nmb" type="text" readonly />
		</div>

		<div class="search">
			<button type="submit" class="btn crud-submit-show" data-toggle="modal" data-target="#show-all">
			  Show all
			</button>
			<input id="liveSearch" type="search" placeholder="Search...">
		</div>

		<table class="table table-bordered">
			<thead>
			    <tr>
				<th>ID</th>
				<th class="hide">Process ID</th>
				<th>Kind</th>
				<th>Type</th>
				<th>Subtype</th>
				<th>Domain</th>
				<th>Name</th>
				<th>Short Desc.</th>
				<th class="hide">Description</th>
				<th class="hide">Parameter Desc.</th>
				<th class="hide">Destination Desc.</th>
				<th>Action</th> <!--  style="width:250px" -->
			    </tr>
			</thead>
			<tbody id="myTable">
			</tbody>
		</table>

		<!--<input type="text" name="idStandard" value="<?php echo $idStandard; ?>" />-->

		<!-- Create Item Modal -->
		<div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_view-application-packet.php" method="POST">

						<input id="user_role" type="hidden" name="role" value="<?php echo $idRole; ?>" />

						<div class="form-group">
							<input type="hidden" name="idStandard" value="<?php echo $idStandard; ?>" />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Packet:</label>
							<select id="sel_packet_create" name="idPacket" class="form-control" data-error="Please enter packet." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

                        <br/>

						<div class="form-group">
							<button type="submit" class="btn crud-submit btn-success">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>

		  </div>
		</div>

		<!-- Edit Item Modal -->
<!--
		<div class="modal fade" id="edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Item</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-packet.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">
-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">Standard ID:</label>
							<input type="text" name="idStandard" class="form-control" data-error="Please enter idStandard." readonly required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parent ID:</label>
							<input type="text" name="idParent" class="form-control" data-error="Please enter idParent." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Process ID:</label>
							<input type="text" name="idProcess" class="form-control" data-error="Please enter idProcess." required />
							<div class="help-block with-errors"></div>
						</div>
-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">Process/APID:</label>
							<select id="sel_process" name="idProcess" class="form-control" data-error="Please enter process/APID." required>
								<option value="select"></option>
							</select>
                            <div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Kind:</label>
							<select id="sel_kind" name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<select id="sel_type" name="type" class="form-control" data-error="Please enter type." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Subtype:</label>
							<input type="text" name="subtype" class="form-control" data-error="Please enter subtype." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control" data-error="Please enter domain." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter shortDesc." required />
							<div class="help-block with-errors"></div>
						</div>
-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" data-error="Please enter description." required></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control" data-error="Please enter descParam." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control" data-error="Please enter descDest." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Code:</label>
							<input type="text" name="code" class="form-control" data-error="Please enter code." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Setting:</label>
							<textarea name="setting" class="form-control" data-error="Please enter setting."></textarea>
							<div class="help-block with-errors"></div>
						</div>
-->
<!--
						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>
-->

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-detail-tm-prv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Detail of TM</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-application-packet-detail-tm-prv.php" method="put">

						<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control-min" data-error="Please enter domain." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Packet:</label>
                            &nbsp;&nbsp;&nbsp;TM (
							<input type="text" name="type" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter type." readonly />
                            /
                            <input type="text" name="subtype" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter subtype." readonly />
                            )&nbsp;
                            <input type="text" name="name" class="form-control-min" data-error="Please enter name." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control-min" data-error="Please enter shortDesc." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control-min-full" data-error="Please enter description." readonly ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control-min-full" data-error="Please enter descParam." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control-min-full" data-error="Please enter descDest." readonly />
							<div class="help-block with-errors"></div>
						</div>

                        <hr>

						<div class="form-group">
							<label class="control-label" for="title">Enable Check:</label>
							<textarea name="enablecheck" class="form-control" data-error="Please enter enable check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Ready Check:</label>
							<textarea name="readycheck" class="form-control" data-error="Please enter ready check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Repeat Check:</label>
							<textarea name="repeatcheck" class="form-control" data-error="Please enter repeat check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Update Action:</label>
							<textarea name="updateaction" class="form-control" data-error="Please enter update action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-detail-tm-prv">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-detail-tc-prv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Detail of TC</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-application-packet-detail-tc-prv.php" method="put">

						<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control-min" data-error="Please enter domain." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Packet:</label>
                            &nbsp;&nbsp;&nbsp;TC (
							<input type="text" name="type" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter type." readonly />
                            /
                            <input type="text" name="subtype" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter subtype." readonly />
                            )&nbsp;
                            <input type="text" name="name" class="form-control-min" data-error="Please enter name." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control-min" data-error="Please enter shortDesc." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control-min-full" data-error="Please enter description." readonly ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control-min-full" data-error="Please enter descParam." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control-min-full" data-error="Please enter descDest." readonly />
							<div class="help-block with-errors"></div>
						</div>

                        <hr>

						<div class="form-group">
							<label class="control-label" for="title">Acceptance Check:</label>
							<textarea name="acceptancecheck" class="form-control" data-error="Please enter acceptance check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Ready Check:</label>
							<textarea name="readycheck" class="form-control" data-error="Please enter ready check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Start Action:</label>
							<textarea name="startaction" class="form-control" data-error="Please enter start action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Progress Action:</label>
							<textarea name="progressaction" class="form-control" data-error="Please enter progress action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Termination Action:</label>
							<textarea name="terminationaction" class="form-control" data-error="Please enter termination action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Abort Action:</label>
							<textarea name="abortaction" class="form-control" data-error="Please enter abort action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-detail-tc-prv">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>
        
		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-detail-tm-usr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Detail of TM</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-application-packet-detail-tm-usr.php" method="put">

						<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control-min" data-error="Please enter domain." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Packet:</label>
                            &nbsp;&nbsp;&nbsp;TM (
							<input type="text" name="type" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter type." readonly />
                            /
                            <input type="text" name="subtype" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter subtype." readonly />
                            )&nbsp;
                            <input type="text" name="name" class="form-control-min" data-error="Please enter name." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control-min" data-error="Please enter shortDesc." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control-min-full" data-error="Please enter description." readonly ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control-min-full" data-error="Please enter descParam." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control-min-full" data-error="Please enter descDest." readonly />
							<div class="help-block with-errors"></div>
						</div>

                        <hr>

						<div class="form-group">
							<label class="control-label" for="title">Acceptance Check:</label>
							<textarea name="acceptancecheck" class="form-control" data-error="Please enter acceptance check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Update Action:</label>
							<textarea name="updateaction" class="form-control" data-error="Please enter update action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-detail-tm-usr">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-detail-tc-usr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Detail of TC</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-application-packet-detail-tc-usr.php" method="put">

						<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control-min" data-error="Please enter domain." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Packet:</label>
                            &nbsp;&nbsp;&nbsp;TC (
							<input type="text" name="type" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter type." readonly />
                            /
                            <input type="text" name="subtype" class="form-control-min" style="width:3ch;text-align:center;" data-error="Please enter subtype." readonly />
                            )&nbsp;
                            <input type="text" name="name" class="form-control-min" data-error="Please enter name." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control-min" data-error="Please enter shortDesc." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control-min-full" data-error="Please enter description." readonly ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control-min-full" data-error="Please enter descParam." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control-min-full" data-error="Please enter descDest." readonly />
							<div class="help-block with-errors"></div>
						</div>

                        <hr>

						<div class="form-group">
							<label class="control-label" for="title">Enable Check:</label>
							<textarea name="enablecheck" class="form-control" data-error="Please enter enable check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Ready Check:</label>
							<textarea name="readycheck" class="form-control" data-error="Please enter ready check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Repeat Check:</label>
							<textarea name="repeatcheck" class="form-control" data-error="Please enter repeat check."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Update Action:</label>
							<textarea name="updateaction" class="form-control" data-error="Please enter update action."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-detail-tc-usr">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>

				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="sel_application-standard.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>