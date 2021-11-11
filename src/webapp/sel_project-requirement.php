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

$sql = "SELECT * FROM `docprefix` WHERE `idProject` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows_prefix = mysqli_num_rows($result);

$message = "";
$docPrefix = "";
if ($num_rows_prefix == 1) {
    $row = $result->fetch_assoc();
    $docPrefix = $row['prefix'];
    $docPrefixId = $row['id'];
} else if ($num_rows_prefix == 0) {
    $message = ">>> INSERT PREFIX FOR YOUR DOCUMENTS <<<";
} else {
    $message = "FAILURE: too many (".$num_rows.") prefixes were found.";
}


//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

if(isset($_POST['saveDocPrefix'])){
    $message= "The prefix is saved.";
    if ($num_rows_prefix == 0) {
        $sql = "INSERT INTO `docprefix` (`idProject`, `prefix`) VALUES (".$_POST['idProject'].", '".$_POST['prefix']."')";
        $result = $mysqli->query($sql);
        $docPrefix = $_POST['prefix'];
    } else if ($num_rows_prefix == 1) {
        $sql = "UPDATE `docprefix` SET `prefix`= '".$_POST['prefix']."' WHERE `id` = ".$docPrefixId;
        $result = $mysqli->query($sql);
        $docPrefix = $_POST['prefix'];
    }
    //$message = $sql;
}

$messageCreatePIL = "";
if(isset($_POST['createPIL'])) {
    if(isset($_POST['EDP'])) {
        header( "refresh:0;url=view_dataPack.php?idProject=".$idProject );
        die('');
    }
    if(isset($_POST['MDP'])) {
        header( "refresh:0;url=sel_dataPack-document.php?idProject=".$idProject );
        die('');
    }
    

    $sel_idPackage = $_POST['idPackage'];
    //echo "idPackage = ".$sel_idPackage."<br/>";
    
    if ($sel_idPackage == 0) {
        $messageCreatePIL = "<font color=red>Please select a Package first!</font>";
    } else {
    
    // get list of internal documents from database
    //$sql = "SELECT * FROM `document` AS d, `docversion` as dv, `docdatapack` AS ddp WHERE ddp.idDataPack = ".$$sel_idPackage;
    //$sql = "SELECT * FROM `docdatapack` AS ddp WHERE ddp.idDataPack = ".$sel_idPackage;
    $sql = "SELECT * FROM `docdatapack` AS ddp, `docversion` as dv WHERE ddp.idDocVersion = dv.id AND ddp.idDataPack = ".$sel_idPackage;
    $result = $mysqli->query($sql);
    $output = "idPackage = ".$sel_idPackage." \\n";
    while($row = $result->fetch_assoc()) {
        //echo $row['name']." <br/>";
        $output .= $row['idDocVersion']." ".$row['version'].", ".$row['date']." \\n";
        //echo $row['idDocVersion']." ".$row['version'].", ".$row['date']."<br/>";
    }
    
    //echo '<script type="text/javascript" language="Javascript"> 
    //    console.log(">>> output = '.$output.'");
    //    alert("'.$output.'") 
    //</script> ';
    
        $messageCreatePIL = "<font color=blue>PIL created for Package ".$_POST['idPackage'].".</font>";
    }
}

$messageMngtDoc = "";
$messageCreateRL = "";
if(isset($_POST['createInitialDoc'])) {
    if(isset($_POST['CDV'])) {
        $messageMngtDoc = "New document version created [".$docPrefix."-".$_POST['docType']."-".$_POST['number']." : ".$_POST['title'].", ".$_POST['version1'].", ".$_POST['date']."].";
        
        // check, if initial document already exists
        $sql_CDV_select = "SELECT * FROM `document` WHERE `idProject` = ".$idProject." AND `idDocRelation` = 1 AND `shortName` = '".$_POST['docType']."' AND `number` = '".$_POST['number']."'"; 
        
        $messageMngtDoc .= "<br/>".$sql_CDV_select;
        //$sql_CDV = "INSERT INTO ";
        
        header( "refresh:0;url=write_file_document.php?idProject=".$idProject."&docType=".$_POST['docType']."&number=".$_POST['number'] );
        
        
    } else if(isset($_POST['CRD'])) {
        $messageMngtDoc = "Red-marked document created [version ".$_POST['version1']." -> version ".$_POST['version2']."].";
    } else if(isset($_POST['CRL'])) {
        header( "refresh:0;url=write_file_bibliography.php?idProject=".$idProject );
        //die('');
        $messageCreateRL = "Reference list created [".$_POST['createInitialDoc']."].";
    } else if(isset($_POST['CAL'])) {
        header( "refresh:0;url=write_file_glossary.php?idProject=".$idProject );
        //die('');
        $messageCreateRL = "Acronym list created [".$_POST['createInitialDoc']."].";
    } else {
        $messageMngtDoc = "UNKNOWN ACTIVITY.";
        $messageCreateRL = "UNKNOWN ACTIVITY.";
    }
}

function getColorFromString($dataPackName) {
    $color = "green";
    $nmb = 0;
    
    for($i=0;$i<strlen($dataPackName);$i++) {
        $nmb += ord($dataPackName[$i]);
        //echo '<script type="text/javascript" language="Javascript"> 
        //    console.log(">>> char = '.$dataPackName[$i].': nmb = '.$nmb.'");
        //    </script> ';
    }
    $color_x = $nmb % 8;
    //echo '<script type="text/javascript" language="Javascript"> 
    //    console.log(">>> color = '.$color_x.'");
    //    </script> ';
            
    switch($color_x) {
        case 0:
            $color = "green";
        break;
        case 1:
            $color = "blue";
        break;
        case 2:
            $color = "#CCCC00"; // yellow
        break;
        case 3:
            $color = "red";
        break;
        case 4:
            $color = "magenta";
        break;
        case 5:
            $color = "brown";
        break;
        case 6:
            $color = "gold";
        break;
        case 7:
            $color = "black";
        break;
        default:
            $color = "green";
    }
            
    return $color;
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Project - Requirement Management</title>
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
.divider {
  font-size: 1px;
  background: rgba(0, 0, 0, 0.5);
}

.divider--blue {
  background: #337AB7;
}

.divider--danger {
  background: red;
}

.collapsible {
  cursor: pointer;
}

.active, .collapsible:hover {
  background-color: #555;
}

.content {
  display: none;
}

.w3-container:after,.w3-container:before,.w3-panel:after,.w3-panel:before,.w3-row:after,.w3-row:before,.w3-row-padding:after,.w3-row-padding:before,
.w3-cell-row:before,.w3-cell-row:after,.w3-clear:after,.w3-clear:before,.w3-bar:before,.w3-bar:after{content:"";display:table;clear:both}
.w3-container,.w3-panel{padding:0.01em 16px}.w3-panel{margin-top:16px;margin-bottom:16px}
.w3-pale-red,.w3-hover-pale-red:hover{color:#000!important;background-color:#ffdddd!important}
.w3-pale-green,.w3-hover-pale-green:hover{color:#000!important;background-color:#ddffdd!important}
.w3-pale-yellow,.w3-hover-pale-yellow:hover{color:#000!important;background-color:#ffffcc!important}
<!--.w3-pale-blue,.w3-hover-pale-blue:hover{color:#000!important;background-color:#ddffff!important}-->
.w3-pale-blue,.w3-hover-pale-blue:hover{color:#000!important;background-color:#dcf2fc!important}
.w3-leftbar {border-left:6px solid #ccc!important}
.w3-border-blue,.w3-hover-border-blue:hover{border-color:#2196F3!important}

	</style>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?></h4>
		            <h2>Requirement Management</h2>
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

<br/>

<div>
<h3>Tailoring</h3>

E40 Tailoring <br/>
E40 Documents <br/><br/>

Q80 Tailoring


<h3>Traceability Matrices</h3>

11C Traceability <br/><br/>
 
PUS-A/C Traceability <br/><br/>

Ext./Int. Requ. Traceability <br/><br/>

Int. Requ./Test Traceability
<div>
<?php
echo "HELP<br/>";
echo "<table class='table table-bordered'>";
echo "<thead>";
echo "<tr>";
echo "<td>E \\ I</td>";
echo "<td>I1</td>";
echo "<td>I2</td>";
echo "</tr>";
echo "</thead>";
echo "<tbody id='myRequTable'>";
echo "<tr>";
echo "<td>E1</td>";
echo "<td></td>";
echo "<td></td>";
echo "</tr>";
echo "<tr>";
echo "<td>E2</td>";
echo "<td></td>";
echo "<td></td>";
echo "</tr>";
echo "</tbody>";
echo "</table>";
echo "HELP2<br/>";
?>
</div>

</div>


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
					<a class="a_btn" href="open_project.php?id=<?php echo $idProject; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>


<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
</script>

</body>

</html>