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

$message = "Configuration:\n";
$message .= "\noperating system: ".$os;
$message .= "\npath_to_python: ".$path_to_python;
$message .= "\npath_to_pyscripts: ".$path_to_pyscripts;
$message .= "\npython_cmd: ".$python_cmd;

if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };
$project_name = "";
$application_name = "";
$application_desc = "";

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

	if(isset($_POST['insert'])){
		$message= "The insert function is called.";
	}
	if(isset($_POST['select'])){
		$message="The select function is called.";
	}
	if(isset($_POST['build'])){
		//$message = "The build function is called.";

		//$path_to_python = "C:\\Users\\chris\\Anaconda2\\";
		//$path_to_python = "";
		//$path_to_pyscripts = "py\\";
		//$path_to_pyscripts = "..\\cordetfw\\editor-1.1\\_lib\\libraries\\sys\\python\\src\\";
		//$path_to_pyscripts = ".\\python\\";
		//$cmd = "python ../_lib/libraries/sys/python/src/build_app.py " . $this->sc_temp_project_id . " " . $this->sc_temp_application_id . " 2>&1";
		//$cmd = "python py/build_app.py " . $this->sc_temp_project_id . " " . $this->sc_temp_application_id . " 2>&1";
		//$cmd = $path_to_python."python.exe ".$path_to_pyscripts."run_python.py 2>&1";
		$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."build_app.py ".$idProject." ".$idApplication." 2>&1";

		$file = shell_exec($cmd);
		$file = substr($file, 0, strlen($file)-1);
		$message = $file;

		if (file_exists($file)) {
			lib_dwnFile(true, $file);
			//system('rm -rf ' . escapeshellarg(dirname($file)));
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

	if(isset($_POST['importAcrList'])){
		$messageAcrImport = "The Import Acronym List function is called.\n\n";
	}

	if(isset($_POST['importReqList'])){
		$messageReqImport = "The Import Requirement List function is called.\n\n";
	}

	if(isset($_POST['buildDpList'])){
		$messageDpList = "The build Data Pool CSV function is called.\n\n";
		
		//$path_to_python = "C:\\Users\\chris\\Anaconda2\\";
		//$path_to_pyscripts = "..\\cordetfw\\editor-1.1\\_lib\\libraries\\sys\\python\\src\\";
		$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."build_dp_csv.py ".$idProject." ".$idApplication." 2>&1";
		
		$file = shell_exec($cmd);
		$file = substr($file, 0, strlen($file)-1);
		
		if (file_exists($file)) {
			$messageDpList .= $file."\n\n";
			lib_dwnFile(true, $file);
			//system('rm -rf ' . escapeshellarg(dirname($file)));
		} else {
			$messageDpList .= "Error: File could not be created!\n";
			$messageDpList .= "Following Error occured during execution:\n";
			$messageDpList .= $file;
		}
	}

	if(isset($_POST['importDpList'])){
		$messageDpImport = "The Import Data Pool List function is called.\n\n";
	}
    
    //print_r(array_keys($_POST));
    
	if(isset($_POST['sel_calCurve'])){
        $idCalCurve = $_POST['sel_calCurve'];
        //echo $idCalCurve."<br/>";
    } else {
        $idCalCurve = 0;
    }
    
	if(isset($_POST['sel_action'])){
        $idAction = $_POST['sel_action'];
        $idCalCurve = $_POST['idCalCurve'];
        $idStandard = $_POST['idStandard'];
        //echo $idCalCurve."<br/>";
    } else {
        $idAction = 0;
    }


function doesTableExists($mysqli, $table) {
    $res = mysqli_query($mysqli,"SHOW TABLES LIKE '$table'");
    
    if(isset($res->num_rows)) {
        return $res->num_rows > 0 ? true : false;
    } else return false;
}

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
	<title>Application <?php echo $application_name;?> </title>
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
	<script type="text/javascript">
	
	
      $(document).ready(function () {
        console.log("Document ready event handler");
		
		$('#loader').hide();
		
		const formBuildDpList = document.getElementById("formBuildDpList");
        const log = document.getElementById("log");
		const buttonBuildDpList = document.getElementById("buttonBuildDpList");
        formBuildDpList.addEventListener("submit", logSubmit);
		
		const formBuild = document.getElementById("formBuild");
		const buttonBuild = document.getElementById("buttonBuild");
		formBuild.addEventListener("submit", logSubmitBuild);
		
		const queryString = window.location.search;
        console.log(queryString);
		const urlParams = new URLSearchParams(queryString);
        
        // let divImportCal open, if selCalCurve is selected
		const calopen = urlParams.get('selCalCurve')
        console.log(calopen);
		if (calopen=="1") {
			var x = document.getElementById("divImportCal");
			x.style.display = "block";
		}

        //document.getElementById("buttonBuildDpList").style.visibility = "hidden"; // show: "visible"; hide: "hidden"
		//$("#buttonBuildDpList").hide();
		
      });
	  
/*	  $(document).ready(function() 
{
    $('#loader').hide();

    $('formBuildDpList').submit(function() 
    {
        $('#loader').show();
    }) 
})*/
	
	window.onload = (event) => {
        console.log("page is fully loaded");
		

    };
	
	function logSubmit(event) {
		//$('#loader').show();
		$("#buttonBuildDpList").hide();
		//$("#progressBuildDpList").css({background:'linear-gradient(to right, #25b350, #e6e8ec)'});
		$("#progressBuildDpList").css({background:'linear-gradient(to right, #cc0000, #e6e8ec)'});
		//$("#progressBuildDpList").css({background:'linear-gradient(to right, #25b350, #25b350 ' + Math.round((event.loaded/event.total)*100) + '%, #e6e8ec ' + Math.round((event.loaded/event.total)*100) + '%)'});
		//log.textContent = `Form Submitted! Timestamp: ${event.timeStamp}`;
        //event.preventDefault();
    }
	
	function logSubmitBuild(event) {
		$("#buttonBuild").hide();
		//$("#progressBuild").css({background:'linear-gradient(to right, #25b350, #e6e8ec)'});
		$("#progressBuild").css({background:'linear-gradient(to right, #cc0000, #e6e8ec)'});
		//log.textContent = `Form Submitted! Timestamp: ${event.timeStamp}`;
        //event.preventDefault();
    }
	
		function buildProject(idProject, idApplication) {
			toastr.success('Debug: Project: '+idProject+', Application: '+idApplication, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: START ...', 'Success Alert', {timeOut: 5000}); 



			toastr.success('Debug: Output: '+$file, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: ... END', 'Success Alert', {timeOut: 5000}); 
		}
		
	function myFunction(divName) {
		let divs = ["divBuild", "divImportAcr", "divImportDat", "divImportReq", "divImportCal"];
		
		for (div of divs) {
			//var x = document.getElementById(div);
			window['x'+div] = document.getElementById(div);  // dynamic variable names
			var x = eval('x'+div);
			if (div == divName) {
			    if (x.style.display === "none") {
			        x.style.display = "block";
		        } else {
			        x.style.display = "none";
		        }
			} else {
				x.style.display = "none";
			}
			
		}
		
	}
	</script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
	<style type="text/css">
.progress { <!-- https://codeshack.io/file-upload-progress-bar-js-php/ -->
  height: 20px;
  border-radius: 4px;
  margin: 10px 0;
  background-color: #e6e8ec;
}
@media (min-width: 40em) {
.table { 
   display: table;
   border-spacing: 0.5em;
}
.table-row {display: table-row; }
.table-cell {display: table-cell; }
}

.tooltipAcr {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltipAcr .tooltipAcrText {
  visibility: hidden;
  width: 250px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 5px;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltipAcr:hover .tooltipAcrText {
  visibility: visible;
}

.tooltipDat {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltipDat .tooltipDatText {
  visibility: hidden;
  width: 450px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 5px;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltipDat:hover .tooltipDatText {
  visibility: visible;
}

.tooltipReq1 {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltipCal {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltipCal .tooltipCalText {
  visibility: hidden;
  width: 250px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 5px;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltipCal:hover .tooltipCalText {
  visibility: visible;
}
	</style>
</head>
<body onload="myFunction('')">

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Project <?php echo $project_name;?> - Application <?php echo $application_name;?></h2>
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
					<form data-toggle="validator" action="api/update_spec-application.php" method="put">

						<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
						<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">

						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." value="<?php echo $application_name; ?>" readonly />
							<div class="help-block with-errors"></div>
						</div>
 
 						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<textarea name="desc" class="form-control" data-error="Please enter description." readonly ><?php echo $application_desc; ?></textarea>
							<div class="help-block with-errors"></div>
						</div>
 
						<!--<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Save</button>
						</div>-->

		      		</form>

		      </div>
			  
                <form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 5px 15px;" onsubmit="this.action='sel_application-standard.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'">
                    <input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
                    <input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
                    <input type="submit" name="openRelationList" value="Relations" class="btn btn-success crud-submit-open-rellist"> 
                    <?php } else { ?>
                    <input type="submit" name="openRelationList" value="Relations" class="btn btn-success crud-submit-open-rellist" disabled> 
                    <?php } ?>
                    &nbsp;&nbsp;&nbsp; Relations of Application to Standard(s) (service user or service provider)
                </form>

                <!--<br/>-->

                <form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 5px 15px 15px 15px;" onsubmit="this.action='sel_application-component.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'">
                    <input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
                    <input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
                    <input type="submit" name="openComponentList" value="Components" class="btn btn-success crud-submit-open-cmplist"> 
                    <?php } else { ?>
                    <input type="submit" name="openComponentList" value="Components" class="btn btn-success crud-submit-open-cmplist" disabled> 
                    <?php } ?>
                    &nbsp;&nbsp;&nbsp;  Components to be generated as source code, instrument database files, tex tables for documentation, etc. 
                </form>


                <br/><br/><br/>
			  
			  
				<button class="btn btn-primary edit-item" onclick="myFunction('divBuild')">Build</button>
				&nbsp;&nbsp;&nbsp;
				<button class="btn btn-primary edit-item" onclick="myFunction('divImportAcr')">Import Acronym List</button>
				&nbsp;&nbsp;&nbsp;
				<button class="btn btn-primary edit-item" onclick="myFunction('divImportDat')">Import Data Pool List</button>
				&nbsp;&nbsp;&nbsp;
				<button class="btn btn-primary edit-item" onclick="myFunction('divImportReq')">Import Requirement List</button>
				<?php if (doesTableExists($mysqli, "calibration")) { ?>
				&nbsp;&nbsp;&nbsp;
				<button class="btn btn-primary edit-item" onclick="myFunction('divImportCal')">Import Calib. Curve</button>
				<?php } ?>
				
				<br/><br/><br/>



                <!-- ### Import Calibration Curve ################################################ -->
                <div id="divImportCal">

                <?php if (doesTableExists($mysqli, "calibration")) { ?>
                <form id="formTypGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;" method="POST" action="open_application.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>&selCalCurve=1">
                  <div class="table" style="margin-bottom:0px;">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Calibration Curve Type
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <?php if ($idCalCurve == 0) { ?>
                          <select name="sel_calCurve" class="form-control" onchange="this.form.submit()">
                              <option value="0" selected>--- Please select ---</option>
                              <option value="1">Numerical</option>
                              <option value="2">Polynomial</option>
                              <option value="3">Logarithmical</option>
                          </select>
                          <!--<input type="hidden"  name="selCalCurve" class="form-control" value="1" required />-->
                          <?php } else if ($idCalCurve == 1) { ?>
                          <select name="sel_calCurve" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1" selected>Numerical</option>
                              <option value="2">Polynomial</option>
                              <option value="3">Logarithmical</option>
                          </select>
                          <?php } else if ($idCalCurve == 2) { ?>
                          <select name="sel_calCurve" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1">Numerical</option>
                              <option value="2" selected>Polynomial</option>
                              <option value="3">Logarithmical</option>
                          </select>
                          <?php } else if ($idCalCurve == 3) { ?>
                          <select name="sel_calCurve" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1">Numerical</option>
                              <option value="2">Polynomial</option>
                              <option value="3" selected>Logarithmical</option>
                          </select>
                          <?php    
                                } ?>
                              
                     </div>
                  </div>
                  </div>
                </form>
                
                <?php if ($idCalCurve > 0) { ?>
                <form id="formActGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;" method="POST" action="open_application.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>&selCalCurve=1&selAction=1">
                
                  <input type="hidden" name="idCalCurve" class="form-control" value="<?php echo $idCalCurve; ?>" required>
                  
                  <input type="hidden" name="idAction" class="form-control" value="<?php echo $idAction; ?>" required>
                  
                  <div class="table" style="margin-bottom:0px;">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Action
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <?php if ($idAction == 0) { ?>
                          <select name="sel_action" class="form-control" onchange="this.form.submit()">
                              <option value="0" selected>--- Please select ---</option>
                              <option value="1">Add new calibration</option>
                              <?php if ($idCalCurve=="1") { ?>
                              <option value="2">Add new points</option>
                              <?php } else { ?>
                              <option value="2">Add new coefficients</option>
                              <?php } ?>
                              <option value="3">Replace calibration</option>
                          </select>
                          <!--<input type="hidden"  name="selCalCurve" class="form-control" value="1" required />-->
                          <?php } else if ($idAction == 1) { ?>
                          <select name="sel_action" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1" selected>Add new calibration</option>
                              <?php if ($idCalCurve=="1") { ?>
                              <option value="2">Add new points</option>
                              <?php } else { ?>
                              <option value="2">Add new coefficients</option>
                              <?php } ?>
                              <option value="3">Replace calibration</option>
                          </select>
                          <?php } else if ($idAction == 2) { ?>
                          <select name="sel_action" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1">Add new calibration</option>
                              <?php if ($idCalCurve=="1") { ?>
                              <option value="2" selected>Add new points</option>
                              <?php } else { ?>
                              <option value="2" selected>Add new coefficients</option>
                              <?php } ?>
                              <option value="3">Replace calibration</option>
                          </select>
                          <?php } else if ($idAction == 3) { ?>
                          <select name="sel_action" class="form-control" onchange="this.form.submit()">
                              <!--<option value="0">--- Please select ---</option>-->
                              <option value="1">Add new calibration</option>
                              <?php if ($idCalCurve=="1") { ?>
                              <option value="2">Add new points</option>
                              <?php } else { ?>
                              <option value="2">Add new coefficients</option>
                              <?php } ?>
                              <option value="3" selected>Replace calibration</option>
                          </select>
                           <?php    
                                } ?>
                              
                     </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Standard
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <select id="sel_standard" name="idStandard" class="form-control" data-error="Please enter standard." required>
<?php
$sql = "SELECT * FROM `standard` WHERE idProject = $idProject";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo "<h3>Standards</h3> $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        /*echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_standard.php?idProject=$id&idStandard=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";*/
        echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
    }
} else {
    echo "0 results";
}
?>
                          </select>
                      </div>
                  </div>
                  </div>
                </form>
                <?php } ?>  <!-- END idCalCurve > 0 -->
                
                <?php if ($idCalCurve > 0 AND $idAction > 0) { ?>

				<form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 1px 15px 15px 15px;"
				onsubmit="this.action='view_calibration-import.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'"> <!-- action="view_acronym-import.php" --> <!-- padding: top right bottom left -->
                
                  <input type="hidden" name="idCalCurve" class="form-control" value="<?php echo $idCalCurve; ?>" required>
                  
                  <input type="hidden" name="idAction" class="form-control" value="<?php echo $idAction; ?>" required>
                  
                  <input type="hidden" name="idStandard" class="form-control" value="<?php echo $idStandard; ?>" required>
                
                  <div class="table">
                  
                  <?php if ($idAction > 1) { ?>
                  
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Existing Calibration Curve
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <select name="sel_calId" class="form-control" required>
                              <option value="">--- Please select ---</option>
<?php
$type = $idCalCurve - 1;
$sql = "SELECT * FROM `calibration` WHERE idStandard = $idStandard AND type = $type";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo "<h3>Standards</h3> $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        /*echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_standard.php?idProject=$id&idStandard=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";*/
        echo "<option value='".$row["id"]."'>".$row["name"]." / ".$row["shortDesc"]."</option>";
    }
} else {
    echo "0 results";
}
?>
                          </select>
                     </div>
                  </div>
                  <?php } ?>  <!-- END idAction > 1 -->
                  
                  <?php if ($idAction == 1) { ?>

                  <div class="table-row">
                  <div class="table-cell" style="width:20%;">
                  <hr>
                  </div>
                  <div class="table-cell" style="width:80%;">
                  <hr>
                  </div>
                  </div>
                  
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Name
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="name" class="form-control" data-error="Please enter name." required />
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Short Description
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="shortDesc" class="form-control" data-error="Please enter short description." required />
                      </div>
                  </div>

                  <?php if ($idCalCurve == 1) { ?>

                  <div class="table-row">
                  <div class="table-cell" style="width:20%;">
                  <hr>
                  </div>
                  <div class="table-cell" style="width:80%;">
                  <hr>
                  </div>
                  </div>

                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Enigneering Format
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <!--<input type="text" name="engFmt" class="form-control" data-error="Please enter raw format." required />-->
                          <select name="engFmt" class="form-control" required>
                              <option value="I">signed Integer</option>
                              <option value="U">unsigned Integer</option>
                              <option value="R" selected>Real</option>
                          </select>
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Raw Format
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <!--<input type="text" name="rawFmt" class="form-control" data-error="Please enter raw format." required />-->
                          <select name="rawFmt" class="form-control" required>
                              <option value="I">signed Integer</option>
                              <option value="U" selected>unsigned Integer</option>
                              <option value="R">Real</option>
                          </select>
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Radix
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <!--<input type="text" name="radix" class="form-control" data-error="Please enter raw format." required />-->
                          <select name="radix" class="form-control" required>
                              <option value="D" selected>Decimal</option>
                              <option value="H">Hexadecimal</option>
                              <option value="O">Octal</option>
                          </select>
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Unit
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="unit" class="form-control" data-error="Please enter raw format." />
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          N Curve
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="ncurve" class="form-control" data-error="Please enter raw format." required />
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Interpolation
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <!--<input type="text" name="inter" class="form-control" data-error="Please enter raw format." required />-->
                          <select name="inter" class="form-control" required>
                              <option value="P" selected>Interpolate / extrapolate</option>
                              <option value="F">Disabled</option>
                          </select>
                      </div>
                  </div>
                  
                  <?php } else if ($idCalCurve == 2) { ?>
                  
                  <?php } else if ($idCalCurve == 3) { ?>
                  
                  <?php } ?> <!-- END idCalCurve == 1 -->
                  
                  <?php } ?> <!-- END idAction == 1 -->

                  <div class="table-row">
                  <div class="table-cell" style="width:20%;">
                  <hr>
                  </div>
                  <div class="table-cell" style="width:80%;">
                  <hr>
                  </div>
                  </div>

                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Select Calibration Curve to Upload
                          <div class="tooltipCal">INFO
                            <span class="tooltipCalText">
                            <?php if ($idCalCurve=="1") { ?>
                            Format: e.g. "[X Value] \t [Y Value]" with a CSV delimiter of "\t"
                            <?php } else if ($idCalCurve=="2") { ?>
                            Format: e.g. "Pol1 \t [Pol1 Value]" with a CSV delimiter of "\t"
                            <?php } else if ($idCalCurve=="3") { ?>
                            Format: e.g. "Log1 \t [Log1 Value]" with a CSV delimiter of "\t"
                            <?php } ?>
                            </span>
                          </div>
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  
                  
                  </div>
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="importCalList" value="Import Calib. Curve" class="btn btn-success crud-submit-import-codlist">
                    <?php } else { ?>
                    <input type="submit" name="importCalList" value="Import Calib. Curve" class="btn btn-success crud-submit-import-codlist" disabled>
                    <?php } ?>
                    <br/><br/>
					<textarea class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageCalImport)){ echo $messageCalImport;}?></textarea>
				</form>
                <?php } ?> <!-- END idCalCurve > 0 AND idAction > 0 -->
                
                <?php } ?> <!-- END doesTableExists($mysqli, "calibration") -->

                </div>
				
                <!-- ### Import Requirement List ################################################ -->
				<div id="divImportReq">

				<form id="formReqGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;"> <!-- padding: top right bottom left -->

                  <div class="table">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          CSV delimiter
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="csvDelimiter" class="form-control" data-error="Please enter DP domain." required />
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Standard
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <select id="sel_reqlist" name="idReqList" class="form-control" data-error="Please enter requirement list." required>
                              <option value='1'>ECSS E40 Tailoring</option>
                              <option value='2'>ECSS E40 Documents</option>
                              <option value='3'>ECSS Q80 Tailoring</option>
                              <option value='4'>other ...</option>
                              <option value='5'>ONCE: ECSS-E-ST-70-11C(31July2008) [OPER]</option>
                              <option value='6'>ONCE: ECSS-E-ST-40C(6March2009) [E40 Std.]</option>
                              <option value='7'>ONCE: ECSS-Q-ST-80C(6March2009) [Q80 Std.]</option>
                              <option value='8'>ONCE: ECSS-E-70-41A(30Jan2003) [PUS-A]</option>
                              <option value='9'>ONCE: ECSS-E-ST-70-41C(15April2016) [PUS-C]</option>
                              <option value='10'>Functional Tailoring [OIRD]</option>
                              <option value='11'>PUS-C Tailoring [OIRD]</option>
                              <option value='12'>Project Requirements for SRS</option>
                              <option value='13'>Project TM/TC Commands</option>
                              <option value='14'>Project TM/TC Reports</option>
                              <option value='15'>Mission/Instrument Level Requirements</option>
                              <option value='16'>Subsystem Level Requirements</option>
                          </select>
                      </div>
                  </div>
                  <!--<div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          First Row is Header
                      </div>
                      <div class="table-cell" style="width:80%;">
                        <fieldset name="firstRow">-->
                          <!--<input type="checkbox" name="firstRow" class="form-control" data-error="Please enter if first row is header." />-->
                          <!--<input type="radio" id="fry" name="firstRowY">
                          <label for="fry">Yes</label>&nbsp;
                          <input type="radio" id="frn" name="firstRowN" checked>
                          <label for="frn">No</label>
                        </fieldset>
                      </div>
                  </div>-->
                  </div>

                </form>
				<form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 1px 15px 15px 15px;"
				onsubmit="this.action='view_requirement-import.php?idProject=<?php echo $idProject; ?>&'+Array.prototype.slice.call(formReqGET.elements).map(function(val){return val.name + '=' + val.value}).join('&');"> <!-- action="view_acronym-import.php" --> <!-- padding: top right bottom left -->
                  <div class="table">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          First Row is Header
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <!--<input type="checkbox" name="firstRow" class="form-control" data-error="Please enter if first row is header." />-->
                          <input type="radio" id="fry" name="firstRow" value="on">
                          <label for="fry">Yes</label>&nbsp;
                          <input type="radio" id="frn" name="firstRow" value="" checked>
                          <label for="frn">No</label>
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Select Requirements List to Upload
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  </div>
					
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="importReqList" value="Import Requirement List" class="btn btn-success crud-submit-import-reqlist">
                    <?php } else { ?>
                    <input type="submit" name="importReqList" value="Import Requirement List" class="btn btn-success crud-submit-import-reqlist" disabled>
                    <?php } ?>
                    <br/><br/>
					<textarea class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageReqImport)){ echo $messageReqImport;}?></textarea>
				</form>

                <br/><br/>
				
				
				</div> 
				
                <!-- ### Import Acronym List #################################################### -->
				<div id="divImportAcr">
				

				<form id="formAcrGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;"> <!-- padding: top right bottom left -->

                  <div class="table" style="margin-bottom:0px;">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          CSV delimiter
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="csvDelimiter" class="form-control" data-error="Please enter DP domain." required />
                      </div>
                  </div>
                  </div>

                </form>
				<form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 1px 15px 15px 15px;"
				onsubmit="this.action='view_acronym-import.php?idProject=<?php echo $idProject; ?>&'+Array.prototype.slice.call(formAcrGET.elements).map(function(val){return val.name + '=' + val.value}).join('&');"> <!-- action="view_acronym-import.php" --> <!-- padding: top right bottom left -->
                  <div class="table">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Select Acronym List to Upload
						  <div class="tooltipAcr">INFO
                            <span class="tooltipAcrText">Format: e.g. "Acronym ; Description" with a CSV delimiter of ";" </span>
                          </div>
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  </div>
					
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="importAcrList" value="Import Acronym List" class="btn btn-success crud-submit-import-acrlist">
                    <?php } else { ?>
                    <input type="submit" name="importAcrList" value="Import Acronym List" class="btn btn-success crud-submit-import-acrlist" disabled>
                    <?php } ?>
                    <br/><br/>
					<textarea class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageAcrImport)){ echo $messageAcrImport;}?></textarea>
				</form>

                <br/><br/>


				</div> 
				
                <!-- ### Import Data Pool List ################################################## -->
				<div id="divImportDat">


<!--
<form action="upload_DpList.php" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>
-->

				<!--<form  action="upload_DpList.php" method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px;">-->
				<form id="formGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;"> <!-- padding: top right bottom left -->

                  <div class="table" style="margin-bottom:0px;">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Data Pool Domain
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="dpDomain" class="form-control" data-error="Please enter DP domain." required />
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Standard
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <select id="sel_standard" name="idStandard" class="form-control" data-error="Please enter standard." required>
<?php
$sql = "SELECT * FROM `standard` WHERE idProject = $idProject";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo "<h3>Standards</h3> $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        /*echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_standard.php?idProject=$id&idStandard=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";*/
        echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
    }
} else {
    echo "0 results";
}
?>
                          </select>
                      </div>
                  </div>
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Data Pool Item Name Prefix
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="text" name="dpNamePrefix" class="form-control" data-error="Please enter DP name prefix." />
                      </div>
                  </div>
                  </div>

                </form>
				<form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 1px 15px 15px 15px;"
				onsubmit="this.action='view_datapool-import.php?'+Array.prototype.slice.call(formGET.elements).map(function(val){return val.name + '=' + val.value}).join('&');"> <!-- action="view_datapool-import.php" --> <!-- padding: top right bottom left -->
                  <div class="table">
                  <div class="table-row">
                      <div class="table-cell" style="width:20%;">
                          Select Data Pool List to Upload
						  <div class="tooltipDat">INFO
                            <span class="tooltipDatText">Format: e.g. "name | pid | datatype | bitsize |multiplicity | kind | value | description | domain" with a CSV delimiter of "|" </span>
                          </div>
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  </div>
					
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="importDpList" value="Import Data Pool List" class="btn btn-success crud-submit-import-dplist">
                    <?php } else { ?>
                    <input type="submit" name="importDpList" value="Import Data Pool List" class="btn btn-success crud-submit-import-dplist" disabled>
                    <?php } ?>
                    <br/><br/>
					<textarea class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageDpImport)){ echo $messageDpImport;}?></textarea>
				</form>

				<br/><br/>
				

				</div> 

                <!-- ### Build ################################################################## -->
				<div id="divBuild">
					



				<form id="formBuildDpList" method="post" style="background-color: #d1d1d1; padding: 15px;">
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="buildDpList" value="Build Data Pool CSV" class="btn btn-success crud-submit-build-dplist" id="buttonBuildDpList">
                    <?php } else { ?>
                    <input type="submit" name="buildDpList" value="Build Data Pool CSV" class="btn btn-success crud-submit-build-dplist" disabled>
                    <?php } ?>
					<div id="progressBuildDpList" class="progress"></div>
					<textarea class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageDpList)){ echo $messageDpList;}?></textarea>
				</form>
				<p id="log"></p>
				<!--<div id="loader" style="position: fixed; top:0; left:0; width:100%; height: 100%; background: url('img/ajax-loader.gif') center center #efefef"></div>-->

				<br/><br/>

				<!--<button type="build" class="btn btn-success crud-submit-build" onclick="buildProject(<?php echo $idProject; ?>, <?php echo $idApplication; ?>);return false;">Build</button>-->
				<form  id="formBuild" method="post" style="background-color: #d1d1d1; padding: 15px;">
					<!--<input type="text" name="txt" value="<?php if(isset($message)){ echo $message;}?>" >
					<!--<input type="submit" name="insert" value="insert">
					<input type="submit" name="select" value="select" >-->
                    <?php if ($idRole < 4) { ?>
					<input type="submit" name="build" value="Build" class="btn btn-success crud-submit-build" id="buttonBuild">
                    <?php } else { ?>
                    <input type="submit" name="build" value="Build" class="btn btn-success crud-submit-build" disabled>
                    <?php } ?>
					<div id="progressBuild" class="progress"></div>
					<textarea rows="25" class="form-control" style="background-color: #e1e1e1;" readonly ><?php if(isset($message)){ echo $message;}?></textarea>
				</form>
				
				<br/><br/>
				
				
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
*/
/*
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

				<div class="topcorner_left">
<?php include 'logos.php'; ?>
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