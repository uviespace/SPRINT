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
require 'int/config.php';
require 'int/global_functions.php';

if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["idParameter"])) { $idParameter  = $_GET["idParameter"]; } else { $idParameter=''; };
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

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

$idRole = get_max_access_level($mysqli, $idProject, $userid, $userEmail);

if(isset($_POST['import'])){
    //echo "IMPORT<br/>";

   if(isset($_FILES['importfile'])){
      $errors= array();
      $file_name = $_FILES['importfile']['name'];
      $file_size =$_FILES['importfile']['size'];
      $file_tmp =$_FILES['importfile']['tmp_name'];
      $file_type=$_FILES['importfile']['type'];
      $file_name_explode = explode('.',$_FILES['importfile']['name']);
      $file_ext=strtolower(end($file_name_explode));
      
      /*
      echo "file_name: ".$file_name."<br/>";
      echo "file_size: ".$file_size."<br/>";
      echo "file_tmp: ".$file_tmp."<br/>";
      echo "file_type: ".$file_type."<br/>";
      echo "file_ext: ".$file_ext."<br/>";*/
	  
	  if(!file_exists($file_tmp)) {
          $errors[]="No file selected. Please choose the file first!";
	  } else {
          $extensions = array("csv","txt");
          if(in_array($file_ext,$extensions)=== false){
             $errors[]="extension not allowed, please choose a CSV or TXT file.";
          }
	  }
      
      /*
      $extensions= array("jpeg","jpg","png");
      
      if(in_array($file_ext,$extensions)=== false){
         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
      }
      
      if($file_size > 2097152){
         $errors[]='File size must be excately 2 MB';
      }
      */
      
      if(empty($errors)==true){
          move_uploaded_file($file_tmp, $path_to_imports.$file_name);
          //echo "Success";
           
          $cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."import_csv.py datapool ".$idStandard." 100 ".$path." 2>&1";
          
          $res = shell_exec($cmd);
          $message = $res;
          
          if(file_exists($path_to_imports."Datapool.csv")) {
              $datetime = date("YmdHis");
              rename($path_to_imports."Datapool.csv", $path_to_imports."Datapool_".$datetime.".csv");
          } else {
			  $errors[]="Wrong filename: '".$file_name."' Should be 'Datapool'.";
			  $message="";
		  }
	  
	  }else{
          //print_r($errors);
      }
      
   }

}

if(isset($_POST['export'])){
    //echo "EXPORT<br/>";
	//$message = "The build function is called.";

	$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."export_csv.py datapool ".$idStandard." 100 2>&1";  // kind = 100: export both 3 and 4

	$file = shell_exec($cmd);
    $fileToDelete = substr($file, 0, strpos($file, '\\'));   // TODO: works only with Windows
	$file = substr($file, 0, strlen($file)-1);
	$message = $file;

	if (file_exists($file)) {
		lib_dwnFile(true, $file);
		//system('rm -rf ' . escapeshellarg(dirname($file)));
        rmdirr($fileToDelete);
	} else {
		$message = "Error: Consistency check failed!\n";
		$message .= "Please correct the errors as listed hereafter:\n";
		$message .= $file;
	}
}

function lib_dwnFile($clean, $file) {
	//$_SESSION['scriptcase']['form_Application_mob']['contr_erro'] = 'on';
	if (file_exists($file)) {
		$size = filesize($file);
		header('Content-Description: File Transfer');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Length: ' . $size);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: public');
		header('Connection: Keep-Alive');
		if ($clean) {
			@ob_end_clean();
			@ob_end_clean();
		}
		flush();
		readfile($file);
	}
}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        rmdirr("$dirname/$entry");
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Datapool</title>
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
    <script type="text/javascript">
        
        var infoarea;
        
        window.onload=function(){
            var el = document.getElementById( 'file-upload' );
            infoArea = document.getElementById( 'file-upload-filename' );
            
            if(el){
                el.addEventListener( 'change', showFileName );
            }
            
        }
        
        function showFileName( event ) {

            // the change event gives us the input it occurred in 
            var input = event.srcElement;
            
            // the input has an array of files in the `files` property, each one has a name that you can use. We're just using the name here.
            var fileName = input.files[0].name;
            
            // use fileName however fits your app best, i.e. add it into a div
            infoArea.textContent = 'File name: ' + fileName;

        }
        
        function txt_onfocus(txt){txt.style.backgroundColor='white';}

        function myFunctionP(e,i) {
            document.getElementById("values_"+i).value = e.target.value;
            document.getElementById("values_"+i).style.backgroundColor='#DBF9DB';  // light rose green
            select_box = document.getElementById("sel_consts_"+i);
            select_box.selectedIndex = 0;  // -1 empty
        }
        
        function myFunctionC(e,i) {
            document.getElementById("values_"+i).value = e.target.value;
            document.getElementById("values_"+i).style.backgroundColor='LightYellow';
            select_box = document.getElementById("sel_params_"+i);
            select_box.selectedIndex = 0;  // -1 empty
        }

    </script>
	<script type="text/javascript" src="int/livesearch.js"></script>
	<script type="text/javascript" src="js/item-ajax_view-datapool.js"></script>
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
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Datapool</h2>
		        </div>
                <?php if ($idRole < 4) { ?>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
                <?php } ?>
		    </div>
		</div>

        <form class="import-export" method="post" enctype="multipart/form-data">
            <?php if ($idRole < 4) { ?>
                <input type="submit" name="export" value="Export" class="btn btn-success crud-submit-export">
            <?php } else { ?>
                <input type="submit" name="export" value="Export" class="btn btn-success crud-submit-export" disabled>
            <?php } ?>
            <?php if ($idRole < 4) { ?>
                <input type="file" id="file-upload" name="importfile" style="display:none" />
                <label for="file-upload" class="btn btn-primary browse-file">Choose File</label>
                <input type="submit" name="import" value="Import" class="btn btn-success crud-submit-import">
                <div id="file-upload-filename"></div>
            <?php } else { ?>
                <input type="submit" name="import" value="Import" class="btn btn-success crud-submit-import" disabled>
            <?php } ?>
            <?php
                if(empty($errors)==false){
                    echo "<font color='red'>";
                    print_r($errors);
                    echo "</font><br/>";
                }
                if(empty($message)==false){
                    echo "<font color='green'>";
                    print_r($message);
                    echo "</font><br/>";
                }
            ?>
        </form>

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
				<th style="min-width:50px;">ID</th>
				<th style="min-width:120px;">Domain</th>
				<th style="min-width:180px;">Name</th> 
				<th>Short Description</th>
				<th style="min-width:50px;">Kind</th> <!-- Dropdown -->
				<th style="min-width:55px;">Data-<br>type</th> <!-- Dropdown -->
				<th style="min-width:60px;">Multi-<br>plicity</th>
				<th width="120px">Value</th>
				<th style="min-width:60px;">Unit</th>
				<th width="210px">Action</th>
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
					<form data-toggle="validator" action-data="api/create_view-datapool.php" method="POST">
<!--
						<div class="form-group">
							<label class="control-label" for="title">ID:</label>
							<input type="text" name="id" class="form-control" data-error="Please enter id." required />
							<div class="help-block with-errors"></div>
						</div>
-->
						<input id="user_role" type="hidden" name="role" value="<?php echo $idRole; ?>" />

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
							<label class="control-label" for="title">Short Description:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." />
							<div class="help-block with-errors"></div>
						</div>

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
					<form data-toggle="validator" action="api/update_view-datapool.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

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
							<label class="control-label" for="title">Short Description:</label>
							<input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Kind:</label>
							<!--<input type="text" name="kind" class="form-control" data-error="Please enter kind." />-->
							<!--<select name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="3">Par</option>
								<option value="4">Var</option>
							</select>-->
							<select id="sel_kind" name="kind" class="form-control" data-error="Please enter kind." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Datatype:</label>
							<!--<input type="text" name="datatype" class="form-control" data-error="Please enter datatype." />-->
							<select id="sel_datatype" name="idType" class="form-control" data-error="Please enter datatype." required>
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
							<input type="text" name="value" class="form-control" data-error="Please enter value." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Unit:</label>
							<input type="text" name="unit" class="form-control" data-error="Please enter unit." />
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

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-values" tabindex="-1" role="dialog" aria-labelledby="myModalLabelValues">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabelValues">Edit Values</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_view-datapool-values.php" method="put">

                <div class="form-group">
                    for datapool item&nbsp;&nbsp;
                    <input id="domain-val_id" type="text" name="domain-val" style="border: 0;background-color:white;font-weight:bold;" readonly /><b> / </b>
                    <input id="name-val_id" type="text" name="name-val" style="border: 0;background-color:white;font-weight:bold;" readonly />
                    &nbsp;&nbsp;(&nbsp;multiplicity: 
                    <input id="multiplicity-val_id" type="text" name="multiplicity-val" style="border: 0;background-color:white;font-weight:bold;" readonly />
                    )
                </div>

		      			<input type="hidden" name="id" class="edit-id">

<!--
						<div class="form-group">
							<label class="control-label" for="title">Domain:</label>
							<input type="text" name="domain-val" class="form-control" data-error="Please enter domain." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name-val" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>
-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">Multiplicity:</label>
							<input type="text" name="multiplicity-val" class="form-control" data-error="Please enter multiplicity." />
							<div class="help-block with-errors"></div>
						</div>
-->
<!--
						<div class="form-group">
							<label class="control-label" for="title">Value:</label>
							<input type="text" name="value-val" class="form-control" data-error="Please enter value." readonly />
							<div class="help-block with-errors"></div>
						</div>
-->

						<div class="form-group">
							<label class="control-label" for="title">Values:</label>
                            <!--<div id='dat'></div>-->
                            <div id='response'>Response:<br/></div>
							<div class="help-block with-errors"></div>
						</div>
                        
                        <div>&nbsp;</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit-values">Submit</button>
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
                    <?php if ($idParameter=='') { ?>
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
                    <?php } else { ?>
					<a class="a_btn" href="sel_parameter-calibration.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
                    <?php } ?>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>