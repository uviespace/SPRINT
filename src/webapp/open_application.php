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

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

	if(isset($_POST['insert'])){
		$message= "The insert function is called.";
	}
	if(isset($_POST['select'])){
		$message="The select function is called.";
	}
	if(isset($_POST['build'])){
		//$message = "The build function is called.";

		$path_to_python = "C:\\Users\\chris\\Anaconda2\\";
		//$path_to_pyscripts = "py\\";
		$path_to_pyscripts = "..\\cordetfw\\editor-1.1\\_lib\\libraries\\sys\\python\\src\\";
		//$cmd = "python ../_lib/libraries/sys/python/src/build_app.py " . $this->sc_temp_project_id . " " . $this->sc_temp_application_id . " 2>&1";
		//$cmd = "python py/build_app.py " . $this->sc_temp_project_id . " " . $this->sc_temp_application_id . " 2>&1";
		//$cmd = $path_to_python."python.exe ".$path_to_pyscripts."run_python.py 2>&1";
		$cmd = $path_to_python."python.exe ".$path_to_pyscripts."build_app.py ".$idProject." ".$idApplication." 2>&1";

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
		
		$path_to_python = "C:\\Users\\chris\\Anaconda2\\";
		$path_to_pyscripts = "..\\cordetfw\\editor-1.1\\_lib\\libraries\\sys\\python\\src\\";
		$cmd = $path_to_python."python.exe ".$path_to_pyscripts."build_dp_csv.py ".$idProject." ".$idApplication." 2>&1";
		
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
		function buildProject(idProject, idApplication) {
			toastr.success('Debug: Project: '+idProject+', Application: '+idApplication, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: START ...', 'Success Alert', {timeOut: 5000}); 



			toastr.success('Debug: Output: '+$file, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: ... END', 'Success Alert', {timeOut: 5000}); 
		}
	</script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
	<style type="text/css">
@media (min-width: 40em) {
.table { 
   display: table;
   border-spacing: 0.5em;
}
.table-row {display: table-row; }
.table-cell {display: table-cell; }
}
	</style>
</head>
<body>

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

				<br/>

                <form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 5px 15px;" onsubmit="this.action='sel_application-standard.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'">
                    <input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
                    <input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <input type="submit" name="openRelationList" value="Relations" class="btn btn-success crud-submit-open-rellist"> &nbsp;&nbsp;&nbsp; Relations of Application to Standard(s) (service user or service provider)
                </form>

                <!--<br/>-->

                <form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 5px 15px 15px 15px;" onsubmit="this.action='sel_application-component.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'">
                    <input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
                    <input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
                    <input type="submit" name="openComponentList" value="Components" class="btn btn-success crud-submit-open-cmplist"> &nbsp;&nbsp;&nbsp;  Components to be generated as source code, instrument database files, tex tables for documentation, etc. 
                </form>

                <br/><br/>

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
					<input type="submit" name="importReqList" value="Import Requirement List" class="btn btn-success crud-submit-import-reqlist"><br/><br/>
					<textarea rows="3" cols="150" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageReqImport)){ echo $messageReqImport;}?></textarea>
				</form>

                <br/><br/>

				<form id="formAcrGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;"> <!-- padding: top right bottom left -->

                  <div class="table">
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
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  </div>
					
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
					<input type="submit" name="importAcrList" value="Import Acronym List" class="btn btn-success crud-submit-import-acrlist"><br/><br/>
					<textarea rows="3" cols="150" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageAcrImport)){ echo $messageAcrImport;}?></textarea>
				</form>

                <br/><br/>

<!--
<form action="upload_DpList.php" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>
-->

				<!--<form  action="upload_DpList.php" method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px;">-->
				<form id="formGET" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px 15px 1px 15px;"> <!-- padding: top right bottom left -->

                  <div class="table">
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
                      </div>
                      <div class="table-cell" style="width:80%;">
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required >
                      </div>
                  </div>
                  </div>
					
					<input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
					<input type="hidden" name="idApplication" class="edit-id" value="<?php echo $idApplication; ?>">
					<input type="submit" name="importDpList" value="Import Data Pool List" class="btn btn-success crud-submit-import-dplist"><br/><br/>
					<textarea rows="3" cols="150" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageDpImport)){ echo $messageDpImport;}?></textarea>
				</form>

				<br/><br/>

				<form  method="post" style="background-color: #d1d1d1; padding: 15px;">
					<input type="submit" name="buildDpList" value="Build Data Pool CSV" class="btn btn-success crud-submit-build-dplist"><br/><br/>
					<textarea rows="3" cols="150" style="background-color: #e1e1e1;" readonly ><?php if(isset($messageDpList)){ echo $messageDpList;}?></textarea>
				</form>

				<br/><br/>

				<!--<button type="build" class="btn btn-success crud-submit-build" onclick="buildProject(<?php echo $idProject; ?>, <?php echo $idApplication; ?>);return false;">Build</button>-->
				<form  method="post" style="background-color: #d1d1d1; padding: 15px;">
					<!--<input type="text" name="txt" value="<?php if(isset($message)){ echo $message;}?>" >
					<!--<input type="submit" name="insert" value="insert">
					<input type="submit" name="select" value="select" >-->
					<input type="submit" name="build" value="Build" class="btn btn-success crud-submit-build"><br/><br/>
					<textarea rows="25" cols="150" style="background-color: #e1e1e1;" readonly ><?php if(isset($message)){ echo $message;}?></textarea>
				</form>
				
				<br/><br/>
				
				
				
				


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