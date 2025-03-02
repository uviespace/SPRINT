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

if (isset($_GET["idParameter"])) { $idParameter  = $_GET["idParameter"]; } else { $idParameter=0; };

$sql = "SELECT * FROM `parameter` WHERE `id` = ".$idParameter;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $parameter_name = $row["name"];
        $parameter_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

$sql = "SELECT c.* FROM `parameter` p, `calibration` c WHERE JSON_VALUE(p.setting, '$.calcurve') = c.id AND p.id = ".$idParameter;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

$calcurve = false;
if ($result->num_rows > 0) {
    $calcurve = true;
    // output data of each row
/*    while($row = $result->fetch_assoc()) {
         echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["shortDesc"]. "<br/>";
        $calibration_name = $row["name"];
        $calibration_shortDesc = $row["shortDesc"];
        
    }*/
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
	<title>CORDET Editor - Calibrations for Parameter <?php echo $parameter_name; ?></title>
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
	<script type="text/javascript" src="js/item-ajax_view-parameter-calibration.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Calibration for Parameter <?php echo $parameter_name; ?></h2>
		        </div>
                <?php if ($idRole < 4 && !$calcurve) { ?>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Add Calibration
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
                <th>Parameter ID</th>
				<th>Type</th>
				<th>Name</th>
				<th>Short Description</th>
				<th>Setting</th>
				<th width="200px">Action</th>
			    </tr>
			</thead>
			<tbody id="myTable">
			</tbody>
		</table>

		<ul id="pagination" class="pagination-sm"></ul>

		<!-- Create Item Modal -->
		<div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_view-parameter-calibration.php" method="POST">

						<input id="user_role" type="hidden" name="role" value="<?php echo $idRole; ?>" />

						<div class="form-group">
							<input type="hidden" name="idParameter" value="<?php echo $idParameter; ?>" />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Calibration Curve:</label>
							<select id="sel_calcurve_create" name="calcurve" class="form-control" data-error="Please enter calibration curve." required>
								<option value="select"></option>
							</select>
                            <br/>
						</div>

<!--
						<input id="user_role" type="hidden" name="role" value="<?php echo $idRole; ?>" />
-->
						<!--<div class="form-group">
							<label class="control-label" for="title">ID:</label>
							<input type="text" name="id" class="form-control" data-error="Please enter id." required />
							<div class="help-block with-errors"></div>
						</div>-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">idParameter:</label>
							<input type="text" name="idParameter" class="form-control" value="<?php echo $idParameter ?>" data-error="Please enter type." readonly />
							<div class="help-block with-errors"></div>
						</div>


						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<input type="text" name="type" class="form-control" data-error="Please enter type." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Lower Value:</label>
							<input type="text" name="lvalue" class="form-control" data-error="Please enter lower value." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Higher Value:</label>
							<input type="text" name="hvalue" class="form-control" data-error="Please enter higher value." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Setting:</label>
							<textarea name="setting" class="form-control" data-error="Please enter setting."></textarea>
							<div class="help-block with-errors"></div>
						</div>
-->
						<div class="form-group">
							<button type="submit" class="btn crud-submit btn-success">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>

		  </div>
		</div>

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Item</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-parameter-calibration.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<input type="hidden" name="idParameter" value="<?php echo $idParameter; ?>" />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Calibration Curve:</label>
							<select id="sel_calcurve" name="calcurve" class="form-control" data-error="Please enter calibration curve." required>
								<option value="select"></option>
							</select>
                            <br/>
						</div>
<!--
						<div class="form-group">
							<label class="control-label" for="title">idParameter:</label>
							<input type="text" name="idParameter" class="form-control" value="<?php echo $idParameter ?>" data-error="Please enter type." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<input type="text" name="type" class="form-control" data-error="Please enter type." required />
							<div class="help-block with-errors"></div>
						</div>

		      			<div class="form-group">
							<label class="control-label" for="title">Lower Value:</label>
							<input type="text" name="lvalue" class="form-control" data-error="Please enter lower value." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Higher Value:</label>
							<input type="text" name="hvalue" class="form-control" data-error="Please enter higher value." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Setting:</label>
							<textarea name="setting" class="form-control" data-error="Please enter setting."></textarea>
							<div class="help-block with-errors"></div>
						</div>
-->
						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Submit</button>
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
					<a class="a_btn" href="sel_parameter-calibration.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>