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

if (isset($_GET["id"])) { $id  = $_GET["id"]; } else { $id=0; };
if (isset($_GET["action"])) { $action  = $_GET["action"]; } else { $action=""; };
$project_name = "Noname";
$idProject = $id;

$sql = "SELECT * FROM `project` WHERE `id`=".$id;

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

//echo doesTableExists($mysqli, "projectacronym");
//echo doesTableExists($mysqli, "acronym");

if ($action == "exp_acr") {
    //echo "Export List of Acronyms ...<br/>";
    
    $path_tmp = "documentation/out/"; // could also be tmp/
    $filename = "acr_".$project_name.".csv";
    $file = $path_tmp . $filename;
    
    // get file content
    $result = getAcronyms($mysqli, $idProject);
    
    $newcontent = "";
    $delimiter = ";";
    $shortList = false;
    while ($row = $result->fetch_assoc()) {
        if ($shortList) {
            $newcontent .= $row['name'].$delimiter.$row['shortDesc']."\n"; 
        } else {
            $newcontent .= $row['name'].$delimiter.$row['shortDesc'].$delimiter.$row['desc']."\n"; 
        }
    }
    
    // open, write and close file
    $myfile = fopen($file, "w");
    fwrite($myfile, $newcontent);
    fclose($myfile);

    // open/download file in browser
    
    // Header content type
    header('Content-type: 	text/csv');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
    
    // delete file
    unlink($file);
    
    // end page
    die('');

} else if ($action == "exp_ref") {
    //echo "Export List of References ...<br/>";
    
    $path_tmp = "documentation/out/"; // could also be tmp/
    $filename = "ref_".$project_name.".csv";
    $file = $path_tmp . $filename;
    
    // get file content
    $result = getReferences($mysqli, $idProject);
    
    $newcontent = "";
    $delimiter = ";";
    $shortList = false;
    while ($row = $result->fetch_assoc()) {
        if ($shortList) {
            $newcontent .= $row['name'].$delimiter.$row['shortName']."\n"; 
        } else {
            $newcontent .= $row['name'].$delimiter.$row['shortName'].$delimiter.$row['number'].$delimiter.$row['identifier'].$delimiter.$row['version'].$delimiter.$row['date']."\n"; 
        }
    }
    
    // open, write and close file
    $myfile = fopen($file, "w");
    fwrite($myfile, $newcontent);
    fclose($myfile);

    // open/download file in browser
    
    // Header content type
    header('Content-type: 	text/csv');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
    
    // delete file
    unlink($file);
    
    // end page
    die('');
} else if ($action == "exp_int_req") {
    //echo "Export List of Internal Requirements ...<br/>";
    
    $project_name_cap = ucfirst(strtolower($project_name));
    
    $path_tmp = "documentation/out/"; // could also be tmp/
    $filename = $project_name_cap."IaswReq.csv";
    $file = $path_tmp . $filename;
    
    // get file content
    $result = getInternalRequirements($mysqli, $idProject);
    
    $newcontent = "Cat;N;Type;Ver;ShortText;Text;Comment;CloseOut;Test;CodeTrace;TopLevelReq\n";
    $delimiter = ";";
    $shortList = false;
    while ($row = $result->fetch_assoc()) {
        $reqId1 = explode("-", $row['requirementId']);
        $reqId2 = explode("/", $reqId1[1]);
        $recCat = $reqId1[0];
        $reqN = $reqId2[0];
        $reqType = $reqId2[1];
        if (count($reqId2)>2) {
            $reqVer = $reqId2[2];
        } else {
            $reqVer = "";
        }
        
        // get top-level requirement(s)
        $topLevelReq = "";
        $result_TLReq = getTopLevelRequirement($mysqli, $idProject, $row['id']);
        $num_rows = mysqli_num_rows($result_TLReq);
        if ($result_TLReq->num_rows > 0) {
            while ($row_TLReq = $result_TLReq->fetch_assoc()) {
                $topLevelReq .= $row_TLReq['requirementId'].",";
            }
            $topLevelReq = substr($topLevelReq, 0, -1);
        }
        
        //$newcontent .= $row['requirementId'].$delimiter;
        $newcontent .= $recCat.$delimiter.$reqN.$delimiter.$reqType.$delimiter.$reqVer.$delimiter;
        if ($shortList) {
            $newcontent .= $row['shortDesc']."\n"; 
        } else {
            $newcontent .= $row['shortDesc'].$delimiter.$row['desc'].$delimiter.$row['notes'].$delimiter.$row['closeOut'].$delimiter.$row['test'].$delimiter.$row['codeTrace'].$delimiter.$topLevelReq."\n"; 
        }
    }
    
    // open, write and close file
    $myfile = fopen($file, "w");
    fwrite($myfile, $newcontent);
    fclose($myfile);
    
    // open/download file in browser
    
    // Header content type
    header('Content-type: 	text/csv');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
    
    // delete file
    unlink($file);
    
    // end page
    die('');
}

function getAcronyms($mysqli, $idProject) {
    $sql = "SELECT ".
      "pa.id AS id, a.id AS idAcronym, a.name, a.shortDesc, a.desc ".
      "FROM ".
      "`projectacronym` AS pa, `acronym` AS a ".
      "WHERE ".
      "pa.idAcronym = a.id AND ".
      "pa.idProject = ".$idProject." ".
      "ORDER BY ".
      "a.name ".
      "ASC";
    
    return $mysqli->query($sql);
}

function getReferences($mysqli, $idProject) {
    $sql = "SELECT ".
      "pd.id AS id, d.id AS idDocument, d.name, d.shortName, d.number, dv.* ".
      "FROM ".
      "`projectdocument` AS pd, `document` AS d, `docversion` AS dv ".
      "WHERE ".
      "dv.idDocument = d.id AND ".
      "pd.idDocument = d.id AND ".
      "pd.idProject = ".$idProject." ".
      "ORDER BY ".
      "d.name ".
      "ASC";
    
    return $mysqli->query($sql);
}

function getInternalRequirements($mysqli, $idProject) {
    $sql = "SELECT ".
      "* ".
      "FROM ".
      "`projectrequirement` AS pr ".
      "WHERE ".
      "pr.idDocRelation = 1 AND ".
      "pr.idProject = ".$idProject." ".
      "ORDER BY ".
      "pr.requirementId ".
      "ASC";
    
    return $mysqli->query($sql);
}

function getTopLevelRequirement($mysqli, $idProject, $idReq) {
    $sql = "SELECT ".
      "pr.requirementId ".
      "FROM ".
      "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
      "WHERE ".
      "rr.idProjectRequirementInternal = ".$idReq." AND ".
      "pr.id = rr.idProjectRequirementExternal AND ".
      "pr.idDocRelation = 2 AND ".
      "pr.idProject = ".$idProject." ".
      "ORDER BY ".
      "pr.requirementId ".
      "ASC";
    
    return $mysqli->query($sql);
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

if(isset($_POST['import'])){
    //echo "IMPORT<br/>";

   if(isset($_FILES['importfile'])){
	   
	  $files = array_filter($_FILES['importfile']['name']); //Use something similar before processing files.
	  // Count the number of uploaded files in array
      $total_count = count($_FILES['importfile']['name']);
	  $message = "count: ".$total_count;
	  
	  $timestamp = time();
      $datum = date("YmdHis", $timestamp);
	  $dir_of_imported_project = $path_to_imports."Standard_".$datum;
	  mkdir($dir_of_imported_project, 0700);
	  
	  $extensions = array("csv","txt");
			  
	  // Loop through every file
      for( $i=0 ; $i < $total_count ; $i++ ) {
          //The temp file path is obtained
          $tmpFilePath = $_FILES['importfile']['tmp_name'][$i];
		  //Check 
          $file_name = $_FILES['importfile']['name'][$i];
          $file_size =$_FILES['importfile']['size'][$i];
          $file_tmp =$_FILES['importfile']['tmp_name'][$i];
          $file_type=$_FILES['importfile']['type'][$i];
          $file_name_explode = explode('.',$_FILES['importfile']['name'][$i]);
          $file_ext=strtolower(end($file_name_explode));
      
	      if(!file_exists($file_tmp)) {
              $errors[]="No file selected. Please choose the file first!";
	      } else {
              if(in_array($file_ext,$extensions)=== false){
                 $errors[]=$file_name.": extension not allowed, please choose a CSV or TXT file.";
              }
	      }
		  
          //A file path needs to be present
          if ($tmpFilePath != ""){
              //Setup our new file path
              $newFilePath = $dir_of_imported_project."/" . $_FILES['importfile']['name'][$i];
              //File is uploaded to temp dir
              if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                  //Other code goes here
			  }
          }
      }
	  
      if(empty($errors)==true){
          //echo "Success";
          
          $cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."import_csv.py standard ".$idProject." ".$path."Standard_".$datum." 2>&1";
          
          $res = shell_exec($cmd);
          //$message = $res;
		  $message .= " | Import successful";
 
      }else{
          //print_r($errors);
      }
      
   }

}

if(isset($_POST['export'])){
    //echo "EXPORT<br/>";
	//$message = "The build function is called.";

	$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."export_csv.py project ".$idProject." 2>&1";

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
	<title>Project <?php echo $project_name;?></title>
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
	<script type="text/javascript" src="js/item-ajax.js"></script>
	<style type="text/css">

        .badge {
            position: relative;
            top: -11px;
            left: -10px;
            border: 0px solid black;
            border-radius: 75%;
            background-color: green;
            font-size: 7px;
        }

	</style>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Project <?php echo $project_name;?></h2>
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

		<form  method="post" style="background-color: #d1d1d1; padding: 15px;" enctype="multipart/form-data">
            <?php if ($idRole < 4) { ?>
			    <input type="submit" name="export" value="Export Project" class="btn btn-success crud-submit-export">
            <?php } else { ?>
                <input type="submit" name="export" value="Export Project" class="btn btn-success crud-submit-export" disabled>
            <?php } ?>
			<?php if ($idRole < 4) { ?>
                <input type="file" id="file-upload" name="importfile[]" multiple="multiple" style="display:none" />
                <label for="file-upload" class="btn btn-primary browse-file">Choose File</label>
                <input type="submit" name="import" value="Import Standard" class="btn btn-success crud-submit-import">
                <div id="file-upload-filename"></div>
            <?php } else { ?>
                <input type="submit" name="import" value="Import Standard" class="btn btn-success crud-submit-import" disabled>
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

<?php

$sql = "SELECT * FROM `application` WHERE idProject = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Applications</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_application.php?idProject=$id&idApplication=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    //echo "0 results";
}

?>

<?php if ($idRole < 4) { ?>
<a href="view_project-application.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Applications ...</button></a>
<?php } else { ?>
<button style="width:180px;color:gray;" disabled>Manage Applications ...</button>
<?php } ?>

<br/>

<?php

$sql = "SELECT * FROM `standard` WHERE idProject = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Standards</h3>";
//echo $num_rows hits<br/><br/>";

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
    //echo "0 results";
}

?>

<?php if ($idRole < 4) { ?>
<a href="view_project-standard.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Standards ...</button></a>
<?php } else { ?>
<button style="width:180px;color:gray;" disabled>Manage Standards ...</button>
<?php } ?>

<br/><br/>

<?php if ($idRole < 4) { ?>
<h3>Document Management</h3>

<a href="sel_project-documentation.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Documents ...</button></a>

<br/><br/>

<a href="view_project-acronyms.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Acronyms ...</button></a>
<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_acr"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectacronym") AND doesTableExists($mysqli, "acronym")) { echo mysqli_num_rows(getAcronyms($mysqli, $idProject)); } else { echo "--"; } ?></span></span></a>
&nbsp;&nbsp;&nbsp;

<a href="view_project-references.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage References ...</button></a>
<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_ref"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectacronym") AND doesTableExists($mysqli, "acronym")) { echo mysqli_num_rows(getReferences($mysqli, $idProject)); } else { echo "--"; } ?></span></span></a>
&nbsp;&nbsp;&nbsp;

<a href="view_project-organisations.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Organisations ...</button></a>

<br/><br/>

<h3>Requirement Management</h3>

<a href="sel_project-requirement.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Tailoring and Traceability</button></a>

<br/><br/>

<a href="view_project-requirements-external.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Ext. Requ.S ...</button></a>
<!--<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_acr"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectacronym") AND doesTableExists($mysqli, "acronym")) { echo mysqli_num_rows(getAcronyms($mysqli, $idProject)); } else { echo "0"; } ?></span></span></a>-->
&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a href="view_project-requirements-external-requ.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Ext. Requ.R ...</button></a>
<!--<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_ref"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectdocument") AND doesTableExists($mysqli, "document") AND doesTableExists($mysqli, "docversion")) { echo mysqli_num_rows(getReferences($mysqli, $idProject)); } else { echo "0"; } ?></span></span></a>-->

<br/><br/>

<a href="view_project-requirements-internal.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Int. Requ.S ...</button></a>
<!--<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_ref"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectdocument") AND doesTableExists($mysqli, "document") AND doesTableExists($mysqli, "docversion")) { echo mysqli_num_rows(getReferences($mysqli, $idProject)); } else { echo "0"; } ?></span></span></a>-->
&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a href="view_project-requirements-internal-requ.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Int. Requ.R ...</button></a>
<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_int_req"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php if (doesTableExists($mysqli, "projectrequirement") AND doesTableExists($mysqli, "requirementrequirement")) { echo mysqli_num_rows(getInternalRequirements($mysqli, $idProject)); } else { echo "0"; } ?></span></span></a>

<br/><br/>
<?php } ?>

<h3>Contributors / Users</h3>

<?php

$sql = "SELECT u.id, u.name, up.email, up.idRole FROM `user` AS u, `project` AS p, `userproject` AS up WHERE u.id = up.idUser AND p.id = up.idProject AND (up.idRole = 3 OR up.idRole = 4) AND p.id = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:#E8E8E8;'>";
        //echo "<a href='open_user.php?idProject=$id&idApplication=".$row["id"]."' >".$row["id"]." <b>".$row["email"]."</b></a>";
        echo "<font color='Gray'>".$row["id"]." <b>".$row["email"]."</b></font>";
        if ($row["idRole"] == 4) {
            echo "<span style='float:right;'><img src='img/guest.png' />&nbsp;</span>";
        }
        echo "</div>";
    }
    echo "<br/>";
} else {
    //echo "0 results";
}

?>

<?php if ($idRole < 4) { ?>
<a href="view_project-contributor.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Contributors ...</button></a>
<?php } else { ?>
<button style="width:180px;color:gray;" disabled>Manage Contributors ...</button>
<?php } ?>

<br/><br/>

<?php

$sql = "SELECT u.id, u.name, u.email FROM `user` AS u, `project` AS p, `userproject` AS up WHERE u.id = up.idUser AND p.id = up.idProject AND up.idRole = 2 AND p.id = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Owner</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:#E8E8E8;'>";
        //echo "<a href='open_user.php?idProject=$id&idApplication=".$row["id"]."' ><font color='Gray'>".$row["id"]." <b>".$row["name"]."</b> (".$row["email"].")</font></a>";
        echo "<font color='Gray'>".$row["id"]." <b>".$row["name"]."</b> (".$row["email"].")</font>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    //echo "0 results";
}

?>

				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="sel_project.php" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>