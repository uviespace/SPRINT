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
if (isset($_POST["idApplication"])) { $idApplication  = $_POST["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["csvDelimiter"])) { $csvDelimiter  = $_GET["csvDelimiter"]; } else { $csvDelimiter=""; };
if (isset($_GET["idReqList"])) { $idReqList  = $_GET["idReqList"]; } else { $idReqList=""; };
if (isset($_POST["firstRow"])) { $firstRow  = $_POST["firstRow"]; } else { $firstRow=""; };
//echo "firstRow = '".$firstRow."'<br/>";
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

if ($idReqList == 5) { // 11C
    $idDocVersion = 11;
} else if ($idReqList == 6 || $idReqList == 1) { // 40C 
    $idDocVersion = 10;
} else if ($idReqList == 7) { // 80C 
    $idDocVersion = 13;
} else if ($idReqList == 8) { // 41A [PUS-A]
    $idDocVersion = 9;
} else if ($idReqList == 9) { // 41C [PUS-C]
    $idDocVersion = 12;
} else {
    $idDocVersion = 0;
}

// get all requirements in database  
$acrNames[] = null;
$sqlTotal = 
  "SELECT ".
  "* ".
  "FROM ".
  "`requirement` ".
  "WHERE ".
  "idDocVersion = ".$idDocVersion;
  
$result = $mysqli->query($sqlTotal);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "found: ".$row["clause"]."<br/>";
        $acrNames[] = $row["clause"]."|".$row["desc"];
    }
} else {
    //echo "0 results";
}

// get all external requirements in database  
$extReqNames[] = null;
$sqlTotal = 
  "SELECT ".
  "* ".
  "FROM ".
  "`projectrequirement` ".
  "WHERE ".
  "idDocRelation = 2 AND ".
  "idProject = ".$idProject;
  
$result = $mysqli->query($sqlTotal);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "found: ".$row["requirementId"]."<br/>";
        $extReqNames[] = $row["requirementId"]."|".$row["desc"];
    }
} else {
    //echo "0 results";
}


// Check if image file is a actual image or fake image
if(isset($_POST["importAcrList"])) {
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
&& $imageFileType != "gif" && $imageFileType != "csv" && $imageFileType != "txt") {
  //echo "Sorry, only JPG, JPEG, PNG, GIF & CSV, TXT files are allowed.<br/>";
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

function linkedToProject($mysqli, $idProject, $data) {
    $sql = "SELECT id FROM `acronym` WHERE `name` = '".$data[0]."' AND `shortDesc` = '".$data[1]."'";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    $row = $result->fetch_assoc();
    //echo "num_rows: ".$num_rows." | ID: ".$row['id'];
    if ($num_rows==1) {
        $sql_link = "SELECT * FROM `projectacronym` WHERE `idProject` = ".$idProject." AND `idAcronym` = ".$row['id'];
        $result_link = $mysqli->query($sql_link);
        $num_rows_link = mysqli_num_rows($result_link);
        if ($num_rows_link==1) {
            return true;
        }
    }
    return false;
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
	<title>CORDET Editor - Requirement Import</title>
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
	<script type="text/javascript" src="js/item-ajax_view-requirement-import.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Application <?php echo $application_name;?></h4>
		            <h2>Requirement Import</h2>
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
<?php
          if ($idReqList>=4 && $idReqList<10) {
?>
				<th width="5%">ID</th>
				<th width="10%">Column 1</th>
				<th width="50%">Column 2</th>
				<th width="20%">Column 3</th>
				<th width="15%">Action</th>
<?php
          } else if ($idReqList==10 || $idReqList==11) {
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="10%">ECSS Clause</th>
				<th width="25%">Req. Text</th> 
				<th width="15%">Notes</th>
				<th width="15%">Justification</th>
				<th width="4%">Applicability</th>
				<th width="4%">ApplicableToPL</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==12) {
?>
				<th width="4%">ID</th>
				<th width="6%">Cat</th>
				<th width="4%">N</th>
				<th width="4%">Type</th> 
				<th width="4%">Ver</th>
				<th width="10%">Short Text</th>
				<th width="15%">Text</th>
				<th width="10%">Comment</th>
				<th width="10%">Close Out</th>
				<th width="8%">Test</th>
				<th width="10%">Code Trace</th>
				<th width="4%">Top Level Req</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==15) {   // Mission/Instrument Level Requirements
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="15%">Short Text</th>
				<th width="30%">Text</th>
				<th width="28%">Comment</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==16) {   // Subsystem Level Requirements
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="15%">Short Text</th>
				<th width="30%">Text</th>
				<th width="16%">Comment</th>
				<th width="12%">Top Level Req</th>
				<th width="11%">Action</th>
<?php
          } else {
?>
				<th width="4%">ID</th>
				<th width="8%">ECSS ID</th>
				<th width="8%">Req. N</th>
				<th width="14%">Req. Title</th>
				<th width="15%">Req. Text</th> 
				<th width="4%">Applicable</th>
				<th width="12%">Output</th>
				<th width="12%">Remarks</th>
				<th width="12%">ClosOut</th>
				<th width="11%">Action</th>
<?php
          }
?>
			    </tr>
			</thead>
			<tbody id="myTable">
			</tbody>
		</table>

		<table class="table table-bordered" style="word-break:break-all; table-layout:fixed; width:100%;">
			<thead>
			    <tr>
<?php
          if ($idReqList==1) { // ECSS E40 Tailoring
?>
				<th width="4%">ID</th>
				<th width="8%">ECSS ID</th>
				<th width="8%">Req. N</th>
				<th width="14%">Req. Title</th>
				<th width="15%">Req. Text</th> 
				<th width="4%">Applicable</th>
				<th width="12%">Output</th>
				<th width="12%">Remarks</th>
				<th width="12%">ClosOut</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==2) { // ECSS E40 Documents
?>
				<th width="4%">ID</th>
				<th width="8%">ECSS Doc</th>
				<th width="8%">ECSS Clause</th>
				<th width="12%">Cl. Title</th>
				<th width="12%">ECSS Output</th> 
				<th width="4%">Output Appl.</th>
				<th width="10%">IASW Doc</th>
				<th width="8%">Org.1</th>
				<th width="8%">Org.2</th>
				<th width="8%">Org.3</th>
				<th width="8%">Org.4</th>
				<th width="10%">Action</th>
<?php
          } else if ($idReqList==3) { // ECSS Q80 Tailoring
?>
				<th width="4%">ID</th>
				<th width="8%">Req. N</th>
				<th width="14%">Req. Title</th>
				<th width="15%">Req. Text</th> 
				<th width="4%">Applicable</th>
				<th width="12%">Output</th>
				<th width="12%">Remarks</th>
				<th width="12%">ClosOut</th>
				<th width="8%">Responsibility</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList>=4 && $idReqList<10) {
?>
				<th width="5%">ID</th>
				<th width="10%">Column 1</th>
				<th width="50%">Column 2</th>
				<th width="20%">Column 3</th>
				<th width="15%">Action</th>
<?php
          } else if ($idReqList==10 || $idReqList==11) {
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="10%">ECSS Clause</th>
				<th width="25%">Req. Text</th> 
				<th width="15%">Notes</th>
				<th width="15%">Justification</th>
				<th width="4%">Applicability</th>
				<th width="4%">ApplicableToPL</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==12) {
?>
				<th width="4%">ID</th>
				<th width="6%">Cat</th>
				<th width="4%">N</th>
				<th width="4%">Type</th> 
				<th width="4%">Ver</th>
				<th width="10%">Short Text</th>
				<th width="15%">Text</th>
				<th width="10%">Comment</th>
				<th width="10%">Close Out</th>
				<th width="8%">Test</th>
				<th width="10%">Code Trace</th>
				<th width="4%">Top Level Req</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==15) {   // Mission/Instrument Level Requirements
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="15%">Short Text</th>
				<th width="30%">Text</th>
				<th width="28%">Comment</th>
				<th width="11%">Action</th>
<?php
          } else if ($idReqList==16) {   // Subsystem Level Requirements
?>
				<th width="4%">ID</th>
				<th width="12%">Req. ID</th>
				<th width="15%">Short Text</th>
				<th width="30%">Text</th>
				<th width="16%">Comment</th>
				<th width="12%">Top Level Req</th>
				<th width="11%">Action</th>
<?php
          }
?>
			    </tr>
			</thead>
			<tbody id="myTableImport">
<?php

// read CSV file
if($imageFileType == "csv" || $imageFileType == "txt") {
  //echo "Read CSV file...<br/>";
  //$row = 1;
  
$contents = file_get_contents( $target_file );
$contents = str_replace( $csvDelimiter.'{', $csvDelimiter.'"', $contents );
$contents = str_replace( '}'.$csvDelimiter, '"'.$csvDelimiter, $contents );
file_put_contents( $target_file, $contents );



  if (($handle = fopen($target_file, "r")) !== FALSE) {
    if ($firstRow == "on") { // skip first row
        fgetcsv($handle, 4096, $csvDelimiter);
    }
    if ($csvDelimiter == "tab" OR $csvDelimiter == "\\t") {
        $csvDelimiter = "\t";
    }
    $col01_old = "";
    while (($data = fgetcsv($handle, 4096, $csvDelimiter, '"')) !== FALSE) {
    //while (($data = fgetcsv($handle, 4096, "\t", '"')) !== FALSE) {
      $num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "Nr.: ".$data[0]."<br/>";
	  
	  // name | shortDesc | desc 
	  // 0    | 1         | 2    
      
      if ($idReqList==1) {
      if ($num>1) {
          
	  // name, shortDesc
	  $ecssId = $data[0];
      $reqN = $data[1];
      $reqTitle = $data[2];
      $reqText = $data[3];
      $applicable = $data[4];
      $output = $data[5];
      $remarks = $data[6];
      $closeout = $data[7];
      
	  // check if name exists already in Data Pool
	  /*$foundName = false;
      $foundShortDesc = false;
	  foreach ($acrNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqN) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }*/
    
    $ecssClause = $reqN;
    if ($ecssClause!="") {
        // check ECSS Clause
        $foundEcssClause = false;
        $foundEcssClause2 = false;
        $ecssClauses = explode(",", $ecssClause);
        foreach ($ecssClauses as $ecssClauseX) {
            $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 10 AND `clause` = '".trim($ecssClauseX)."'";
            $result = $mysqli->query($sql_ecss);
            if (mysqli_num_rows($result)==1) {
                $foundEcssClause = true;
            } else if (mysqli_num_rows($result)==0) {
                $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 10 AND `clause` = '".trim($ecssClauseX)."a'";
                $result = $mysqli->query($sql_ecss);
                if (mysqli_num_rows($result)==1) {
                    $foundEcssClause = true;
                    $foundEcssClause2 = true;
                }
            } else {
                $foundEcssClause = false;
            }
        }
    }
    

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$ecssId."</td>"; // ECSS ID
	//echo "<td>".$reqN."</td>"; // Req. N
    
    if ($ecssClause!="") {
    if ($foundEcssClause) {
        if ($foundEcssClause2) {
            echo "<td bgcolor='yellow'>".$reqN."[a]</td>"; // Req. N = ECSS Clause (found in database)
        } else {
            echo "<td bgcolor='lightgreen'>".$reqN."</td>"; // Req. N = ECSS Clause (found in database)
        }
    } else {
        echo "<td bgcolor='red'>".$reqN."</td>"; // Req. N = ECSS Clause (NOT found in database)
    }
    } else {
        echo "<td>".$reqN."</td>"; // Req. N = ECSS Clause
    }
    
    if ($num==2) {
        echo "<td>null</td>"; // Req. Title
    } else {
        echo "<td><p style='word-break:normal;'>".$reqTitle."</p></td>"; // Req. Title
    }
    if ($num==2) {
        echo "<td>null</td>"; // Req. Text
    } else {
        echo "<td><p style='word-break:normal;'>".$reqText."</p></td>"; // Req. Text
    }
    if ($num==2) {
        echo "<td>null</td>"; // Applicable
    } else {
        echo "<td><p>".$applicable."</p></td>"; // Applicable
    }
    if ($num==2) {
        echo "<td>null</td>"; // Output
    } else {
        echo "<td><p style='word-break:normal;'>".$output."</p></td>"; // Output
    }
    if ($num==2) {
        echo "<td>null</td>"; // Remarks
    } else {
        echo "<td><p style='word-break:normal;'>".$remarks."</p></td>"; // Remarks
    }
    if ($num==2) {
        echo "<td>null</td>"; // Closeout
    } else {
        echo "<td><p style='word-break:normal;'>".$closeout."</p></td>"; // Closeout
    }
    echo "<td data-id=\"'+value.id+'\">";
    echo "<button class=\"btn btn-success add-item\">Add</button>";
    /*if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item\">Add</button>";
	}*/
	echo "</td>";
	echo "</tr>";
	  
    } else {
        echo "<p>NO, maybe CSV delimiter '".$csvDelimiter."' is not correct! ['".$data[0]."']</p>";
        
        break;
    }
    } else if ($idReqList==2) {
        
        
        
        
    } else if ($idReqList==3) {
      if ($num>1) {
          
	  // name, shortDesc
      $reqN = $data[0];
      $reqTitle = $data[1];
      $reqText = $data[2];
      $applicable = $data[3];
      $output = $data[4];
      $remarks = $data[5];
      $closeout = $data[6];
	  $responsibility = $data[7];
	  // check if name exists already in Data Pool
	  /*$foundName = false;
      $foundShortDesc = false;
	  foreach ($acrNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $name) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            if (explode("|", $acrName)[1] == $shortDesc) {
                $foundShortDesc = true;
            }
			break;
		}
	  }*/
    
    
	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$reqN."</td>"; // Req. N
    if ($num==2) {
        echo "<td>null</td>"; // Req. Title
    } else {
        echo "<td><p style='word-break:normal;'>".$reqTitle."</p></td>"; // Req. Title
    }
    if ($num==2) {
        echo "<td>null</td>"; // Req. Text
    } else {
        echo "<td><p style='word-break:normal;'>".$reqText."</p></td>"; // Req. Text
    }
    if ($num==2) {
        echo "<td>null</td>"; // Applicable
    } else {
        echo "<td><p>".$applicable."</p></td>"; // Applicable
    }
    if ($num==2) {
        echo "<td>null</td>"; // Output
    } else {
        echo "<td><p style='word-break:normal;'>".$output."</p></td>"; // Output
    }
    if ($num==2) {
        echo "<td>null</td>"; // Remarks
    } else {
        echo "<td><p style='word-break:normal;'>".$remarks."</p></td>"; // Remarks
    }
    if ($num==2) {
        echo "<td>null</td>"; // Closeout
    } else {
        echo "<td><p style='word-break:normal;'>".$closeout."</p></td>"; // Closeout
    }
    if ($num==2) {
        echo "<td>null</td>"; // Responsibility
    } else {
        echo "<td><p style='word-break:normal;'>".$responsibility."</p></td>"; // Responsibility
    }
    echo "<td data-id=\"'+value.id+'\">";
    echo "<button class=\"btn btn-success add-item\">Add</button>";
    /*if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item\">Add</button>";
	}*/
	echo "</td>";
	echo "</tr>";
	  
    } else {
        echo "<p>NO, maybe CSV delimiter '".$csvDelimiter."' is not correct! ['".$data[0]."']</p>";
        
        break;
    }
    } else if ($idReqList>=4 && $idReqList<10) {
        
      $col01_match_found = false;
      
      
      //echo preg_match("/^[a-z]\./", "a.");
      
      if ($num>2) {

      // name, shortDesc
      $col01 = $data[0];
      $col02 = $data[1];
      $col03 = $data[2];
      
      // ### LOGIC ###
      if (preg_match("/^$/", $col01) ) {
          $col01_match_found = true;
      }
      if (preg_match("/^NOTE*/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^[1-9]*\.$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^•$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^\([a-z]\)$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^[a-z]*\./", $col01) ) {
            $col01 = $col01_old.substr($col01, 0, -1);
            $col01_match_found = true;
      }
      // #############
      
	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($col01!="") {
	  foreach ($acrNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $col01) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $col02) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }
      
        echo "<tr>";
        echo "<td style=\"color: #fff;\"></td>";
        echo "<td>".$col01."</td>"; // Column 01
        if ($num==1) {
            echo "<td>null</td>"; // Column 02
        } else {
            echo "<td><p style='word-break:normal;'>".$col02."</p></td>"; // Column 02
        }
        if ($num==2) {
            echo "<td>null</td>"; // Column 03
        } else {
            echo "<td><p style='word-break:normal;'>".$col03."</p></td>"; // Column 03
        }
    echo "<td data-id=\"'+value.id+'\">";
    //echo "<button class=\"btn btn-success add-item4\">Add</button>";
    
    if ($col01!="") {
    if ($foundName) {
        if ($foundShortDesc) {
            // check if requirement already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Requirement already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item4\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item4\">Add</button>";
	}
    }
    
    echo "</td>";
        echo "</tr>";

      } else if ($num>1) {
          
	  // name, shortDesc
      $col01 = $data[0];
      $col02 = $data[1];

      // ### LOGIC ###
      if (preg_match("/^$/", $col01) ) {
          $col01_match_found = true;
      }
      if (preg_match("/^NOTE*/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^[1-9]*\.$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^•$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^\([a-z]\)$/", $col01) ) {
          $col02 = $col01." ".$col02;
          $col01 = "";
          $col01_match_found = true;
      }
      if (preg_match("/^[a-z]*\./", $col01) ) {
            $col01 = $col01_old.substr($col01, 0, -1);
            $col01_match_found = true;
      }
      // #############

	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($col01!="") {
	  foreach ($acrNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $col01) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $col02) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }


        echo "<tr>";
        echo "<td style=\"color: #fff;\"></td>";
        echo "<td>".$col01."</td>"; // Column 01
        if ($num==1) {
            echo "<td>null</td>"; // Column 02
        } else {
            echo "<td><p style='word-break:normal;'>".$col02."</p></td>"; // Column 02
        }
        echo "<td></td>"; // Column 03
    echo "<td data-id=\"'+value.id+'\">";
    //echo "<button class=\"btn btn-success add-item4\">Add</button>";
    
    
    if ($col01!="") {
    if ($foundName) {
        if ($foundShortDesc) {
            // check if requirement already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Requirement already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item4\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item4\">Add</button>";
	}
    }
    
    
    echo "</td>";
        echo "</tr>";
      
      } else {
          
	  // name, shortDesc
      $col01 = "";
      $col02 = $data[0];
      
      // ### LOGIC ###
      str_replace("<", "\<", $col02);
      str_replace(">", "\>", $col02);
      $col01_match_found = true;
      // #############

        echo "<tr>";
        echo "<td style=\"color: #fff;\"></td>";
        echo "<td>".$col01."</td>"; // Column 01
        if ($num==0) {
            echo "<td>null</td>"; // Column 02
        } else {
            echo "<td><p style='word-break:normal;'>".$col02."</p></td>"; // Column 02
        }
                echo "<td></td>"; // Column 03
    echo "<td data-id=\"'+value.id+'\">";
    //echo "<button class=\"btn btn-success add-item4\">Add</button>";
    echo "</td>";
        echo "</tr>";
          
          
        //echo "<p>NO, maybe CSV delimiter '".$csvDelimiter."' is not correct! ['".$data[0]."']</p>";
        
        //break;
        

        
    }
    
        if (!$col01_match_found) {
            $col01_old = $col01;
        }
    
        
        
    } else if ($idReqList==10) {
        
        // name, shortDesc
        $reqId = $data[0];
        $ecssClause = $data[1];
        $reqText = $data[3];
        $notes = $data[4];
        $justification = $data[5];
        $applicability = $data[6];
        $applicableToPL = $data[8];

	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($reqId!="") {
	  foreach ($extReqNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqId) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }


    if ($ecssClause!="") {
        // check ECSS Clause
        $foundEcssClause = false;
        $ecssClauses = explode(",", $ecssClause);
        foreach ($ecssClauses as $ecssClauseX) {
            $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 11 AND `clause` = '".trim($ecssClauseX)."'";
            $result = $mysqli->query($sql_ecss);
            if (mysqli_num_rows($result)==1) {
                $foundEcssClause = true;
            } else {
                $foundEcssClause = false;
            }
        }
    }
    

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$reqId."</td>"; // Req. ID
    if ($ecssClause!="") {
    if ($foundEcssClause) {
        echo "<td bgcolor='lightgreen'>".$ecssClause."</td>"; // ECSS Clause (found in database)
    } else {
        echo "<td bgcolor='red'>".$ecssClause."</td>"; // ECSS Clause (NOT found in database)
    }
    } else {
        echo "<td>".$ecssClause."</td>"; // ECSS Clause
    }
    if ($num==2) {
        echo "<td>null</td>"; // Req. Text
    } else {
        echo "<td><p style='word-break:normal;'>".$reqText."</p></td>"; // Req. Text
    }
    if ($num==2) {
        echo "<td>null</td>"; // Notes
    } else {
        echo "<td><p>".$notes."</p></td>"; // Notes
    }
    if ($num==2) {
        echo "<td>null</td>"; // Justification
    } else {
        echo "<td><p style='word-break:normal;'>".$justification."</p></td>"; // Justification
    }
    if ($num==2) {
        echo "<td>null</td>"; // Applicability
    } else {
        echo "<td><p style='word-break:normal;'>".$applicability."</p></td>"; // Applicability
    }
    if ($num==2) {
        echo "<td>null</td>"; // ApplicableToPL
    } else {
        echo "<td><p style='word-break:normal;'>".$applicableToPL."</p></td>"; // ApplicableToPL
    }
    echo "<td data-id=\"'+value.id+'\">";
    if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item10\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item10\">Add</button>";
	}
	echo "</td>";
	echo "</tr>";
        
        
    } else if ($idReqList==11) {
        
        // name, shortDesc
        $reqId = $data[0];
        $ecssClause = $data[1];
        $reqText = $data[3];
        $notes = $data[4];
        $justification = $data[5];
        $applicability = $data[6];
        $applicableToPL = $data[8];

	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($reqId!="") {
	  foreach ($extReqNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqId) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }


    if ($ecssClause!="") {
        // check ECSS Clause
        $foundEcssClause = false;
        $ecssClauses = explode(",", $ecssClause);
        foreach ($ecssClauses as $ecssClauseX) {
            $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 12 AND `clause` = '".trim($ecssClauseX)."'";
            $result = $mysqli->query($sql_ecss);
            if (mysqli_num_rows($result)==1) {
                $foundEcssClause = true;
            } else {
                $foundEcssClause = false;
            }
        }
    }
    

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$reqId."</td>"; // Req. ID
    if ($ecssClause!="") {
    if ($foundEcssClause) {
        echo "<td bgcolor='lightgreen'>".$ecssClause."</td>"; // ECSS Clause (found in database)
    } else {
        echo "<td bgcolor='red'>".$ecssClause."</td>"; // ECSS Clause (NOT found in database)
    }
    } else {
        echo "<td>".$ecssClause."</td>"; // ECSS Clause
    }
    if ($num==2) {
        echo "<td>null</td>"; // Req. Text
    } else {
        echo "<td><p style='word-break:normal;'>".$reqText."</p></td>"; // Req. Text
    }
    if ($num==2) {
        echo "<td>null</td>"; // Notes
    } else {
        echo "<td><p>".$notes."</p></td>"; // Notes
    }
    if ($num==2) {
        echo "<td>null</td>"; // Justification
    } else {
        echo "<td><p style='word-break:normal;'>".$justification."</p></td>"; // Justification
    }
    if ($num==2) {
        echo "<td>null</td>"; // Applicability
    } else {
        echo "<td><p style='word-break:normal;'>".$applicability."</p></td>"; // Applicability
    }
    if ($num==2) {
        echo "<td>null</td>"; // ApplicableToPL
    } else {
        echo "<td><p style='word-break:normal;'>".$applicableToPL."</p></td>"; // ApplicableToPL
    }
    echo "<td data-id=\"'+value.id+'\">";
    if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item10\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item10\">Add</button>";
	}
	echo "</td>";
	echo "</tr>";
        
        
    } else if ($idReqList==12) {
        
        // Cat;N;Type;Ver;ShortText;Text;Comment;CloseOut;Test;CodeTrace;TopLevelReq
        $cat = $data[0];
        $n = $data[1];
        $type = $data[2];
        $ver = $data[3];
        $shortText = $data[4];
        $text = $data[5];
        $comment = $data[6];
        $closeOut = $data[7];
        $test = $data[8];
        $codeTrace = $data[9];
        $topLevelReq = $data[10];

/*
	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($reqId!="") {
	  foreach ($extReqNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqId) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;*/
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
/*            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }*/


/*    if ($ecssClause!="") {
        // check ECSS Clause
        $foundEcssClause = false;
        $ecssClauses = explode(",", $ecssClause);
        foreach ($ecssClauses as $ecssClauseX) {
            $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 12 AND `clause` = '".trim($ecssClauseX)."'";
            $result = $mysqli->query($sql_ecss);
            if (mysqli_num_rows($result)==1) {
                $foundEcssClause = true;
            } else {
                $foundEcssClause = false;
            }
        }
    }*/
    

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$cat."</td>"; // Cat
    echo "<td>".$n."</td>"; // N
    if ($num==2) {
        echo "<td>null</td>"; // Type
    } else {
        echo "<td><p style='word-break:normal;'>".$type."</p></td>"; // Type
    }
    if ($num==2) {
        echo "<td>null</td>"; // Ver
    } else {
        echo "<td><p>".$ver."</p></td>"; // Ver
    }
    if ($num==2) {
        echo "<td>null</td>"; // ShortText
    } else {
        echo "<td><p style='word-break:normal;'>".$shortText."</p></td>"; // ShortText
    }
    if ($num==2) {
        echo "<td>null</td>"; // Text
    } else {
        echo "<td><p style='word-break:normal;'>".$text."</p></td>"; // Text
    }
    if ($num==2) {
        echo "<td>null</td>"; // Comment
    } else {
        echo "<td><p style='word-break:normal;'>".$comment."</p></td>"; // Comment
    }
    if ($num==2) {
        echo "<td>null</td>"; // CloseOut
    } else {
        echo "<td><p style='word-break:normal;'>".$closeOut."</p></td>"; // CloseOut
    }
    if ($num==2) {
        echo "<td>null</td>"; // Test
    } else {
        echo "<td><p style='word-break:normal;'>".$test."</p></td>"; // Test
    }
    if ($num==2) {
        echo "<td>null</td>"; // CodeTrace
    } else {
        echo "<td><p style='word-break:normal;'>".$codeTrace."</p></td>"; // CodeTrace
    }
    if ($num==2) {
        echo "<td>null</td>"; // TopLevelReq
    } else {
        echo "<td><p style='word-break:normal;'>".$topLevelReq."</p></td>"; // TopLevelReq
    }

    echo "<td data-id=\"'+value.id+'\">";
    echo "<button class=\"btn btn-success add-item12\">Add</button>";
    /*if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item10\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item10\">Add</button>";
	}*/
	echo "</td>";
	echo "</tr>";
        
        
    } else if ($idReqList==15) {
        
        $reqId = $data[0];
        $reqShortText = $data[1];
        $reqText = $data[2];
        $comment = $data[3];

	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($reqId!="") {
	  foreach ($extReqNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqId) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$reqId."</td>"; // ReqId
    echo "<td>".$reqShortText."</td>"; // ReqShortText
	echo "<td>".$reqText."</td>"; // ReqText
    echo "<td>".$comment."</td>"; // Comment
    echo "<td data-id=\"'+value.id+'\">";
    if ($foundName) {
        if ($foundShortDesc) {
            // check if requirement already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Requirement already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item15\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item15\">Add</button>";
	}
	echo "</td>";
	echo "</tr>";
    
    } else if ($idReqList==16) {
        
        $reqId = $data[0];
        $shortText = $data[1];
        $text = $data[2];
        $comment = $data[3];
        $topLevelReq = $data[4];

	  // check if name exists already in Data Pool
	  $foundName = false;
      $foundShortDesc = false;
      if ($reqId!="") {
	  foreach ($extReqNames as $acrName) {
        //echo "Value: ".$acrName."<br/>";
	    if(explode("|", $acrName)[0] == $reqId) {
			//echo "Value: ".$acrName."<br/><br/>";
			$foundName = true;
            /*$pos = strpos(explode("|", $acrName)[1], $col02);
            if ($pos === false) {
            } else {*/
            //if (strpos(explode("|", $acrName)[1], $col02)) {
            //if (strpos($col02, explode("|", $acrName)[1])) {
            if (explode("|", $acrName)[1] == $reqText) {
                $foundShortDesc = true;
            }
			break;
		}
	  }
      }

	echo "<tr>";
	echo "<td style=\"color: #fff;\"></td>";
	echo "<td>".$reqId."</td>"; // ReqId
    echo "<td>".$shortText."</td>"; // ShortText
	echo "<td>".$text."</td>"; // Text
    echo "<td>".$comment."</td>"; // Comment
    echo "<td>".$topLevelReq."</td>"; // Top Level Requ.
    echo "<td data-id=\"'+value.id+'\">";
    if ($foundName) {
        if ($foundShortDesc) {
            // check if acronym already linked to this project
            if (linkedToProject($mysqli, $idProject, $data)) {
                echo "<p style='font-size:x-small;word-break:normal;color:blue;'>Item already found in Database and is also linked to this project!</p>";
            } else {
                echo "<p style='font-size:x-small;word-break:normal;color:red;'>Item already found in Database!</p>";
                //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
                echo "<button class=\"btn btn-primary link-item\">Link</button>";
            }
        } else {
            echo "<p style='font-size:x-small;word-break:normal;color:red;'>Acronym already found in Database, but short description differs!</p>";
            //echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
            echo "<button class=\"btn btn-success add-item10\">Add</button>";
        }
	} else {
		//echo "<button data-toggle=\"modal\" data-target=\"#show-item\" class=\"btn btn-primary show-item\">Show</button> ";
		echo "<button class=\"btn btn-success add-item10\">Add</button>";
	}
	echo "</td>";
	echo "</tr>";

    } else {
        echo "<p>NO, Requirement List is not supported! ['".$idReqList."']</p>";
        
        break;
    }
    }
    fclose($handle);
  }
} else {

        echo "<p>NO, file is not a CSV or TXT!</p>";
        
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
					<form data-toggle="validator" action-data="api/create_view-acronym-import.php" method="POST">
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
					<form data-toggle="validator" action="api/update_view-requirement-import.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Requirement ID:</label>
							<input type="text" name="requirementId" class="form-control" data-error="Please enter requirement ID." readonly />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Short Description:</label>
							<textarea name="shortDesc" class="form-control" data-error="Please enter short description." required ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Description:</label>
							<!--<input type="text" name="shortDesc" class="form-control" data-error="Please enter description." />-->
                            <textarea name="desc" class="form-control" style="min-height: 200px;" data-error="Please enter description." ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Notes:</label>
							<!--<input type="text" name="shortDesc" class="form-control" data-error="Please enter description." />-->
                            <textarea name="notes" class="form-control" data-error="Please enter notes." ></textarea>
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