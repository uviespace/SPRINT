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
$project_name = "";

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
    //echo "0 results for projects";
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
	<title>Project - Requirement Management - External vs. Internal</title>
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
	<script type="text/javascript" src="js/item-ajax_view-project-requirement-external-requ.js"></script>
	<style type="text/css">
        .form-control-noborder {
            border: 0;
            width: 450px;
            outline: none;
        }
        .form-control-noborder-h1 {
            border: 0;
            width: 450px;
            outline: none;
            font-size: 18px;
            font-weight: bold;
            color: #337AB7;
        }
	</style>
</head>
<body>
<!-- Back to top button -->
<button
        type="button"
        class="btn btn-info btn-sm"
        id="btn-back-to-top"
        style="background-color: #337AB7; z-index: 1; ">
  <!--<i class="fa fa-arrow-up" style="color:white;"></i>-->
  <img width="22px;" src="img/6622853_rocket_space_icon_white.png" />
  <!--TOP-->
</button>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?></h4>
		            <h2><b>Requirement Management</b> - External Requirements vs. Int. Requ.</h2>
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

		<br/>

		<table class="table table-bordered">
			<thead>
			    <tr>
				<th>ID</th>
                <!--<th>ID ACR</th>-->
				<th style="width: 150px">Requ. ID</th>
                <th style="width: 150px">Subsystem Req.</th>
				<!--<th class=\'hidden\'>Short Description</th>-->
				<!--<th style="width: 20%">Description</th>-->
				<th>Description</th>
				<th>Notes</th>
				<th>Justification</th>
				<th>App.</th>
				<th>App.PL</th>
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
					<h4 class="modal-title" id="myModalLabel">Create Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_view-project-requirement-external-requ.php" method="POST">

						<div class="form-group">
							<input id="project" type="hidden" name="idProject" value="<?php echo $idProject; ?>" />
						</div>

						<!--<div class="form-group">
							<label class="control-label" for="title">Categorie:</label>
							<select id="sel_reqcat_create" name="idReqCat_create" class="form-control" onchange="updateDivReqCatCreate();" data-error="Please enter requirement category." >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">New Categorie:</label>
							<input id="newCat_create" type="text" name="newCat" class="form-control" onFocusOut="updateDivReqNewCatCreate();" data-error="Please enter new requirement category." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Requirement Nr.:</label>
							<input id="reqNr_create" type="text" name="requirementNr" class="form-control" data-error="Please enter requirement number." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<select id="sel_reqtype_create" name="idReqType_create" class="form-control" data-error="Please enter requirement type." required >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Verification:</label>
							<select id="sel_reqverif_create" name="idReqVerif_create" class="form-control" data-error="Please enter requirement verification." required >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>-->

						<div class="form-group">
							<label class="control-label" for="title">Requirement ID:</label>
							<input id="reqId_create" type="text" name="reqId" class="form-control" data-error="Please enter requirement ID." required />
							<div class="help-block with-errors"></div>
						</div>

<div><hr></div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
                            <input id="shortDesc_create" type="text" name="shortDesc" class="form-control" data-error="Please enter short requirement description." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea id="desc_create" name="desc" class="form-control" style="height:175px;" data-error="Please enter requirement description." required  ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Notes:</label>
							<input id="notes_create" type="text" name="notes" class="form-control" data-error="Please enter notes." />
							<div class="help-block with-errors"></div>
						</div>

						<!--<div class="form-group">
							<label class="control-label" for="title">Close Out:</label>
							<input id="closeOut_create" type="text" name="closeOut" class="form-control" data-error="Please enter close out." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Test:</label>
							<input id="test_create" type="text" name="test" class="form-control" data-error="Please enter test." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Code Trace:</label>
							<input id="codeTrace_create" type="text" name="codeTrace" class="form-control" data-error="Please enter code trace." />
							<div class="help-block with-errors"></div>
						</div>-->

<div><hr></div>

						<div class="form-group">
							<label class="control-label" for="title">Subsystem Requirement(s):</label>
							<select id="sel_tlreqid_create" name="idTLReqId_create" class="form-control" data-error="Please enter top-level requirement id." >
								<option value="select"></option>
							</select>
							<select id="sel_tlreqid2_create" name="idTLReqId2_create" class="form-control" data-error="Please enter top-level requirement id nr. 2." >
								<option value="select"></option>
							</select>
							<select id="sel_tlreqid3_create" name="idTLReqId3_create" class="form-control" data-error="Please enter top-level requirement id nr. 3." >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">New Subsystem Requirement ID:</label>
							<input id="newTLReqId_create" name="newTLReqId" class="form-control" data-error="Please enter new subsystem requirement ID." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">New Subsystem Requirement Description:</label>
							<textarea id="newTLReqDesc_create" name="newTLReqDesc" class="form-control" style="height:175px;" data-error="Please enter new subsystem requirement description." ></textarea>
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
					<form data-toggle="validator" action="api/update_view-project-requirement-external.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

                <!--<div class="form-group">
                    for requirement <b><?php echo $requirement; ?></b> (<?php echo $requirementTL; ?>)
                </div>-->

						<div class="form-group">
							<label class="control-label" for="title">Requirement:</label>
							<input name="requirement" class="form-control-noborder-h1" data-error="Please enter short requ." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Requirement TL:</label>
							<input name="requirementTL" class="form-control-noborder" data-error="Please enter short requ TL." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<input id="shortDesc" name="shortDesc" class="form-control" data-error="Please enter short description." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea id="desc" name="desc" class="form-control" style="min-height:75px;" onInput="auto_grow(this)" data-error="Please enter description." required ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Notes:</label>
							<textarea id="notes" name="notes" class="form-control" style="min-height:75px;" onInput="auto_grow(this)" data-error="Please enter notes."  ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Justification:</label>
							<textarea id="justification" name="justification" class="form-control" style="min-height:75px;" onInput="auto_grow(this)" data-error="Please enter justification." ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Applicability:</label>
							<select id="sel_applicability" name="applicability" class="form-control" data-error="Please enter applicability." >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Applicable to Payloads:</label>
							<select id="sel_applicableToPL" name="applicableToPL" class="form-control" data-error="Please enter applicable to payloads." >
								<option value="select"></option>
							</select>
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

		<!-- Change Status Modal -->
		<div class="modal fade" id="change-status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Change Status</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update-change_view-project-requirement-external.php" method="put">

		      			<input type="hidden" name="id" class="change-id">

						<div class="form-group">
							<label class="control-label" for="title">Requirement:</label>
							<input name="requirement_change" class="form-control-noborder-h1" data-error="Please enter short requ." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Subsystem Requirement(s):</label>
							<select id="sel_tlreqid_change" name="idTLReqId_change" class="form-control" data-error="Please enter top-level requirement id." required >
								<option value="select"></option>
							</select>
							<select id="sel_tlreqid2_change" name="idTLReqId2_change" class="form-control" data-error="Please enter top-level requirement id nr. 2." >
								<option value="select"></option>
							</select>
							<select id="sel_tlreqid3_change" name="idTLReqId3_change" class="form-control" data-error="Please enter top-level requirement id nr. 3." >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-change">Submit</button>
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
					<a class="a_btn" href="open_project.php?id=<?php echo $idProject; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>
