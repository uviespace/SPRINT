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

if (isset($_GET["idParent"])) { $idParent  = $_GET["idParent"]; } else { $idParent=0; };

$sql = "SELECT * FROM `packet` WHERE `id` = ".$idParent;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $basePacket_name = $row["name"];
        $basePacket_desc = $row["desc"];
        $basePacket_kind = $row["kind"];
        $basePacket_type = $row["type"];
        $basePacket_subt = $row["subtype"];
    }
} else {
    //echo "0 results";
}

if ($basePacket_kind == 0) {
    $basePacket_kind_str = "TC";
} else if ($basePacket_kind == 1) {
    $basePacket_kind_str = "TM";
} else {
    $basePacket_kind_str = "n/a";
}

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Derivations from Packet <?php echo $basePacket_name; ?></title>
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
	<script type="text/javascript" src="js/item-ajax_view-packet-derived.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Derivations from Packet <?php echo $basePacket_kind_str ."(".$basePacket_type."/".$basePacket_subt.") " . $basePacket_name; ?></h2>
		        </div>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
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
				<!--<th>Standard ID</th>
				<th>Parent ID</th>
				<th>Process ID</th>
				<th>Kind</th>
				<th>Type</th>
				<th>Subtype</th>-->
				<th>Discriminant</th>
				<!--<th>Domain</th>-->
				<th>Name</th>
				<th>Short Desc.</th>
				<th>Description</th>
				<th>Parameter Desc.</th>
				<th>Destination Desc.</th>
				<th>Code</th>
				<!--<th>Setting</th>-->
				<th width="200px">Action</th>
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
					<h4 class="modal-title" id="myModalLabel">Create Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_view-packet-derived.php" method="POST">

                <div class="form-group">
                    for packet <b><?php echo $basePacket_name; ?></b> (<?php echo $idParent; ?>)
                </div>

						<div class="form-group">
							<input type="hidden" name="idStandard" value="<?php echo $idStandard; ?>" />
						</div>

						<div class="form-group">
							<input type="hidden" name="idParent" value="<?php echo $idParent; ?>" />
						</div>

						<div class="form-group">
							<input type="hidden" name="kind" value="0" />
						</div>

						<div class="form-group">
							<input type="hidden" name="subtype" value="0" />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Enumeration Set:</label>
							<select id="sel_enumeration-set_create" name="enumeration-set" class="form-control" onchange="updateDivDiscriminantCreate();" data-error="Please enter parameter." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div id="disc" class="form-group">
							<label class="control-label" for="title">Discriminant:</label>
							<select id="sel_discriminant_create" name="discriminant" class="form-control" onchange="updateDivDiscriminantDescriptionCreate();" data-error="Please enter parameter." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter shortDesc." />
							<div class="help-block with-errors"></div>
						</div>

						<div if="descr" class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea id="descr_textarea_create" name="desc" class="form-control" data-error="Please enter description." required ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control" data-error="Please enter descParam." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control" data-error="Please enter descDest." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Code:</label>
							<input type="text" name="code" class="form-control" data-error="Please enter code." />
							<div class="help-block with-errors"></div>
						</div>

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
					<form data-toggle="validator" action="api/update_view-packet-derived.php" method="put">

                <div class="form-group">
                    for packet <b><?php echo $basePacket_name; ?></b> (<?php echo $idParent; ?>)
                </div>

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Discriminant:</label>
							<select id="sel_discriminant" name="discriminant" class="form-control" onchange="updateDivDiscriminantDescription();" data-error="Please enter parameter." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter shortDesc." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea id="descr_textarea" name="desc" class="form-control" data-error="Please enter description." required ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>

		<!-- Edit Detail Modal -->
		<div class="modal fade" id="edit-detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Detail</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-packet-derived-detail.php" method="put">

                <div class="form-group">
                    for packet <b><?php echo $basePacket_name; ?></b> (<?php echo $idParent; ?>)
                </div>

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Discriminant:</label>
                            <input type="text" name="discriminant" class="form-control-min" data-error="Please enter type." readonly />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
                            <input type="text" name="name" class="form-control-min" data-error="Please enter type." readonly />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Desc.:</label>
                            <input type="text" name="shortDesc" class="form-control-min" data-error="Please enter type." readonly />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
                            <input type="text" name="desc" class="form-control-min" data-error="Please enter type." readonly />
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Parameter Desc.:</label>
							<input type="text" name="descParam" class="form-control" data-error="Please enter descParam." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Destination Desc.:</label>
							<input type="text" name="descDest" class="form-control" data-error="Please enter descDest." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Code:</label>
							<input type="text" name="code" class="form-control" data-error="Please enter code." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit-detail">Submit</button>
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
					<!--<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>-->
					<a class="a_btn" href="sel_packet-derived.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>