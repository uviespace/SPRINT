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

if (isset($_GET["classification"])) { $classification  = $_GET["classification"]; } else { $classification=-1; };
//echo "classification = ".$classification;

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
//echo "Hallo User: ".$userid;

// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;

$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

$userName = $row["name"];

function doesTableExists($mysqli, $table) {
    $res = mysqli_query($mysqli,"SHOW TABLES LIKE '$table'");
    
    if(isset($res->num_rows)) {
        return $res->num_rows > 0 ? true : false;
    } else return false;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Acronyms</title>
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
	<script type="text/javascript">
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight)+"px";
        }
        function adjust_height(id){
            var el = document.getElementById(id) 
            el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight)+"px" : "60px";
        }
	</script>
<?php
if (doesTableExists($mysqli, "classification")) {
?>
	<script type="text/javascript" src="js/item-ajax_mng-acronyms.js"></script>
<?php
}
?>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2><img src="img/acronym_1_64x64.png" width="64" height="64">&nbsp;&nbsp;Acronyms</h2>
		        </div>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
		    </div>
		</div>

        <div>
            <form data-toggle="validator" action="mng_acronym.php" method="POST">
                <div class="form-group">
                    <label class="control-label" for="title">Classification:</label>
                    <select class="form-control" style="width:300px;" name="classification" onChange="window.location='mng_acronym.php?classification='+this.value">
<?php
if ($classification==-1) {
    echo "<option value='-1' selected>ALL</option>";
} else {
    echo "<option value='-1'>ALL</option>";
}
if ($classification==0) {
    echo "<option value='0' selected>NO CLASSIFICATION</option>";
} else {
    echo "<option value='0'>NO CLASSIFICATION</option>";
}
if (doesTableExists($mysqli, "classification")) {
$sql = "SELECT * FROM `classification`";
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
    if ($classification==$row['id']) {
        echo "<option value='".$row['id']."' selected>".$row['name']." (".$row['id'].")</option>";
    } else {
        echo "<option value='".$row['id']."'>".$row['name']." (".$row['id'].")</option>";
    }
}
}
?>
                    </select>
                </div>
            </form>
        </div>

		<ul id="pagination" class="pagination-sm"></ul>

		<div class="result_nmb_rows" style="top:182px;">
			<input id="result_nmb" class="result_nmb" type="text" readonly />
		</div>

		<div class="search" style="top:167px;">
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
				<th>Name</th>
				<th>Short Description</th>
				<th width="450px">Description</th>
				<th>Classification</th>
				<th width="200px">Action</th>
			    </tr>
			</thead>
			<tbody id="myTable">
			</tbody>
		</table>

		<!--<ul id="pagination" class="pagination-sm"></ul>-->

		<!-- Create Item Modal -->
		<div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Create Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_mng-acronym.php" method="POST">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" style="overflow: hidden;" onInput="auto_grow(this)" data-error="Please enter description." ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Classification:</label>
							<select id="sel_classification_create" name="classification" class="form-control" data-error="Please enter classification." >
								<option value="select"></option>
							</select>
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
					<form data-toggle="validator" action="api/update_mng-acronym.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" style="min-height:75px;" onInput="auto_grow(this)" data-error="Please enter description." ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Classification:</label>
							<select id="sel_classification" name="classification" class="form-control" data-error="Please enter classification." >
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

				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>