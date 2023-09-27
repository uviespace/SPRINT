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

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (isset($_POST["idProject"])) { $idProject  = $_POST["idProject"]; } else { $idProject=0; };
//if (isset($_POST["idStandard"])) { $idStandard  = $_POST["idStandard"]; } else { $idStandard=0; };
if (isset($_POST["idApplication"])) { $idApplication  = $_POST["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["csvDelimiter"])) { $csvDelimiter  = $_GET["csvDelimiter"]; } else { $csvDelimiter=""; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["sel_dataType"])) { $sel_dataType  = $_GET["sel_dataType"]; } else { $sel_dataType=""; };
$project_name = "";
$application_name = "";
$application_desc = "";
//echo "_________________________________________________".$csvDelimiter." / ".$sel_dataType;

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

$sql = "SELECT * FROM `application` WHERE `id`=".$idApplication;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $application_name = $row["name"];
        $application_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

/*
$sql = "SELECT * FROM `standard` WHERE `id`=".$idStandard;

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
*/

$sql = "SELECT * FROM `type` WHERE id = ".$sel_dataType;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $datatype_name = $row["name"];
        $datatype_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

// get all enumerations in database  
$enuNames[] = null;
$sqlTotal = 
  "SELECT ".
  "* ".
  "FROM ".
  "`enumeration` ";
  
$result = $mysqli->query($sqlTotal);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "found: ".$row["name"]."<br/>";
        $enuNames[] = $row["name"]."|".$row["value"]."|".$row["desc"];
    }
} else {
    //echo "0 results";
}

// Check if image file is a actual image or fake image
if(isset($_POST["importEnuList"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    //echo "File is an image - " . $check["mime"] . ".<br/>";
    //echo $target_file."<br/>";
    $uploadOk = 0;
    $messageDpImport = $target_file;
  } else {
    //echo "File is not an image.<br/>";
    $uploadOk = 1;
    $messageDpImport = "file detected.<br/>";
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  //echo "Sorry, file already exists.<br/>";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "csv") {
  //echo "Sorry, only JPG, JPEG, PNG, GIF & CSV files are allowed.<br/>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  //echo "Sorry, your file was not uploaded.<br/>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    //echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.<br/>";
  } else {
    echo "Sorry, there was an error uploading your file.<br/>";
  }
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
	<title>CORDET Editor - Enumeration Import</title>
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
	<script type="text/javascript" src="js/item-ajax_view-enumeration-import.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Application <?php echo $application_name;?></h4>
		            <h2><b>Enumeration Import</b> for Datatype <?php echo $datatype_name." (".$sel_dataType.")"; ?></h2>
					<!--<b>Delimiter:</b> <?php echo $csvDelimiter;?><br/><br/>-->
		        </div>
		        <!--<div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>-->
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

		<table class="table table-bordered" style="word-break:break-all;">
			<thead>
			    <tr>
				<th width="5%">ID</th>
				<th width="30%">Name</th>
				<th width="15%">Value</th> 
				<th width="35%">Description</th>
				<th width="150px">Action</th>
			    </tr>
			</thead>
			<tbody id="myTable">
			</tbody>
		</table>

		<table class="table table-bordered" style="word-break:break-all;">
			<thead>
			    <tr>
				<th width="5%">ID</th>
				<th width="30%">Name</th>
				<th width="15%">Value</th> 
				<th width="35%">Description</th>
				<th width="150px">Action</th>
			    </tr>
			</thead>
			<tbody id="myTableImport">
<?php

// read CSV file
if($imageFileType == "csv") {
  //echo "Read CSV file...<br/>";
  //$row = 1;
  $value = 0;
  if (($handle = fopen($target_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
      $num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "Nr.: ".$data[0]."<br/>";
	  
	  // name | value | desc 
	  // 0    | 1     | 2    

      if ($num==1) {
          // create values
          $value += 1;
          $data[1] = $value;
          
          // name, desc
          $name = $data[0];
          $desc = "";
          // check if name exists already in Data Pool
          $foundName = false;
          $foundValue = false;
          $foundDesc = false;
          foreach ($enuNames as $enuName) {
              //echo "Value: ".$enuName."<br/>";
              if(explode("|", $enuName)[0] == $name) {
                  //echo "Value: ".$enuName."<br/><br/>";
                  $foundName = true;
                  if (explode("|", $enuName)[1] == $value) {
                      $foundValue = true;
                  }
                  if (explode("|", $enuName)[2] == $desc) {
                      $foundDesc = true;
                  }
                  break;
              }
          }

      } else if ($num==2) {
          // use values as given
          
          // name, value, desc
          $name = $data[0];
          $value = $data[1];
          $desc = "";
          // check if name exists already in Data Pool
          $foundName = false;
          $foundValue = false;
          $foundDesc = false;
          foreach ($enuNames as $enuName) {
              //echo "Value: ".$enuName."<br/>";
              if(explode("|", $enuName)[0] == $name) {
                  //echo "Value: ".$enuName."<br/><br/>";
                  $foundName = true;
                  if (explode("|", $enuName)[1] == $value) {
                      $foundValue = true;
                  }
                  if (explode("|", $enuName)[2] == $desc) {
                      $foundDesc = true;
                  }
                  break;
              }
          }
      
      } else if ($num>1) {
          // use values as given
          
          // name, desc
          $name = $data[0];
          $value = $data[1];
          $desc = $data[2];
          // check if name exists already in Data Pool
          $foundName = false;
          $foundValue = false;
          $foundDesc = false;
          foreach ($enuNames as $enuName) {
              //echo "Value: ".$enuName."<br/>";
              if(explode("|", $enuName)[0] == $name) {
                  //echo "Value: ".$enuName."<br/><br/>";
                  $foundName = true;
                  if (explode("|", $enuName)[1] == $value) {
                      $foundValue = true;
                  }
                  if (explode("|", $enuName)[2] == $desc) {
                      $foundDesc = true;
                  }
                  break;
              }
          }

      } else {
          echo "<p>NO, maybe CSV delimiter '".$csvDelimiter."' is not correct! ['".$data[0]."']</p>";
          break;
      }



	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$name."</td>"; // name / enumeration
	echo "<td>".$value."</td>"; // value
    if ($num==1 OR $num==2) {
        echo "<td>null</td>"; // description
    } else {
        echo "<td><p style='word-break:normal;'>".$data[2]."</p></td>"; // description
    }
	echo "<td data-id=\"'+value.id+'\">";

    if ($foundName) {
        if ($foundValue) {
            if ($foundDesc) {  // found: name, value, desc
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Enumeration already found in Database and is identical!</p>";
            } else {  // found: name, value
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Enumeration already found in Database, but description differs!</p>";
                echo "<button class=\"btn btn-secondary update-item\">Update</button>";
            }
        } else if ($foundDesc) {  // found: name, desc
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Enumeration already found in Database, but value differs!</p>";
            echo "<button class=\"btn btn-secondary update-item\">Update</button>";
        } else {  // found: name
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Enumeration already found in Database, but value and description differs!</p>";
            echo "<button class=\"btn btn-secondary update-item\">Update</button>";
        }
    } else {  // found nothing
        //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
        echo "<button class=\"btn btn-success add-item\">Add</button>";
    }

	echo "</td>";
	echo "</tr>";



    }
    fclose($handle);
  }
}

?>
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
					<form data-toggle="validator" action-data="api/create_view-enumeration-import.php" method="POST">
<!--
						<div class="form-group">
							<label class="control-label" for="title">ID:</label>
							<input type="text" name="id" class="form-control" data-error="Please enter id." required />
							<div class="help-block with-errors"></div>
						</div>
-->
		      			<input type="hidden" name="idStandard" class="form-control" value="<?php echo $idStandard; ?>">

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
<!--
						<div class="form-group">
							<label class="control-label" for="title">Kind:</label>
							<input type="text" name="kind" class="form-control" data-error="Please enter kind." />
							<div class="help-block with-errors"></div>
						</div>
-->
						<div class="form-group">
							<label class="control-label" for="title">Kind:</label>
							<!--<input type="text" name="kind" class="form-control" data-error="Please enter kind." />-->
<!--							<select name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="3">Par</option>
								<option value="4">Var</option>
							</select>-->
							<select id="sel_kind_create" name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>
						
						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." />
							<div class="help-block with-errors"></div>
						</div>
<!--
						<div class="form-group">
							<label class="control-label" for="title">Datatype:</label>
							<input type="text" name="idType" class="form-control" data-error="Please enter datatype." />
							<div class="help-block with-errors"></div>
						</div>
-->
						<div class="form-group">
							<label class="control-label" for="title">Datatype:</label>
							<!--<input type="text" name="datatype" class="form-control" data-error="Please enter datatype." />-->
							<select id="sel_datatype_create" name="idType" class="form-control" data-error="Please enter datatype." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Multiplicity:</label>
							<input type="text" name="multiplicity" class="form-control" data-error="Please enter multiplicity." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Value:</label>
							<input type="text" name="value" class="form-control" value="0" data-error="Please enter value." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Unit:</label>
							<input type="text" name="unit" class="form-control" data-error="Please enter unit." />
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
					<form data-toggle="validator" action="api/update_view-type-enumeration.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Value:</label>
							<input type="text" name="value" class="form-control" data-error="Please enter value." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<!--<input type="text" name="shortDesc" class="form-control" data-error="Please enter description." />-->
                            <textarea name="desc" class="form-control" data-error="Please enter description." ></textarea>
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

		<!-- Show Item Modal -->
		<div class="modal fade" id="show-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Show Item</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-datapool.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain" class="form-control" data-error="Please enter domain." required readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Kind:</label>
							<!--<input type="text" name="kind" class="form-control" data-error="Please enter kind." />-->
							<!--<select name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="3">Par</option>
								<option value="4">Var</option>
							</select>-->
							<select id="sel_kind_show" name="kind" class="form-control" data-error="Please enter kind." required readonly >
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<!--<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." />-->
                            <textarea name="shortDesc" class="form-control" data-error="Please enter short description." required readonly ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Datatype:</label>
							<!--<input type="text" name="datatype" class="form-control" data-error="Please enter datatype." />-->
							<select id="sel_datatype_show" name="idType" class="form-control" data-error="Please enter datatype." required readonly>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Multiplicity:</label>
							<input type="text" name="multiplicity" class="form-control" data-error="Please enter multiplicity." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Value:</label>
							<input type="text" name="value" class="form-control" data-error="Please enter value." required readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Unit:</label>
							<input type="text" name="unit" class="form-control" data-error="Please enter unit." readonly />
							<div class="help-block with-errors"></div>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>

				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					<a class="a_btn" href="open_application.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>