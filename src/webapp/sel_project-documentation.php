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
	<title>Project - Document Management</title>
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
		            <h2>Document Management</h2>
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

<!-- padding: top right bottom left -->
<!-- padding: top right-and-left bottom -->
<!-- padding: top-and-bottom right-and-left -->
<!-- padding: all-four -->
<form method="post" enctype="multipart/form-data" style="background-color: #d1d1d1; padding: 15px;" onsubmit="this.action='sel_project-documentation.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>'">
    <input type="hidden" name="idProject" class="edit-id" value="<?php echo $idProject; ?>">
    <b>Prefix:</b> <input type='text' name='prefix' style='height: 24px;' value='<?php echo $docPrefix; ?>' />
    <input type="submit" name="saveDocPrefix" value="Save" class="btn btn-success crud-submit-open-rellist">
<?php echo $message; ?>    
</form>

<?php

echo "<br/>";

echo "<form method=\"post\" enctype=\"multipart/form-data\" style=\"background-color: #d1d1d1; padding: 15px;\" onsubmit=\"this.action='sel_project-documentation.php?idProject=".$idProject."'\">";

echo "<b>Package:</b> ";
echo "<select name='idPackage' style='height: 24px;'>";
echo "<option value='0'>--- Please select ---</option>";
    
$sql_proj_dp = "SELECT * FROM `projectdatapack` WHERE `idProject` = ".$idProject;

$result_proj_dp = $mysqli->query($sql_proj_dp);

$num_rows = mysqli_num_rows($result_proj_dp);

//echo "$num_rows hits<br/><br/>";

if ($result_proj_dp->num_rows > 0) {
    // output data of each row
    while($row = $result_proj_dp->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $color = getColorFromString($row['name']);
        echo "<option value='".$row['id']."' style='color:white;background-color:".$color."'>".$row['name']."</oprion>";
    }
}

echo "</select>";

/*
echo "<select name='idPackage' style='height: 24px;'>";
echo "<option value='1'>BRB / SIM - Breadboard / Simulator Release</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
echo "<option value='2'>SRR - System Requirements Review</option>";
echo "<option value='3'>PDR - Preliminary Design Review</option>";
echo "<option value='4'>CDR - Critical Design Review</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
echo "<option value='5'>QR - Qualification Review</option>";
echo "<option value='6'>AR - Acceptance Review</option>";
echo "<option value='7'>QAR - Qualification/Acceptance Review</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
echo "<option value='8'>ORR - Operational Readiness Review</option>";
echo "</select>";
*/

echo "<input type='hidden' name='createPIL' value='1'>";

echo "&nbsp;&nbsp;&nbsp;<a href=''><button style='width:200px;' onClick='form.submit();'>Create Package Item List</button></a>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='img/pdf.png' />&nbsp;<a href='docs/ECSS-Q-ST-80C(6March2009).pdf' target='_blank'>ECSS-Q-ST-80C-6March2009&nbsp;</a>";

echo "<br/>";

echo $messageCreatePIL."<br/>";

echo "<a href='view_dataPack.php?idProject=".$idProject."'><button style='width:200px;' name='EDP'>Edit Data Packages</button></a>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "<a href='sel_dataPack-document.php?idProject=".$idProject."'><button style='width:200px;' name='MDP'>Manage Package Documents</button></a>";



echo "</form>";

echo "<br/>";

//ECSS-Q-ST-80C-6March2009

echo "<form method=\"post\" enctype=\"multipart/form-data\" style=\"background-color: #d1d1d1; padding: 15px;\" onsubmit=\"this.action='write_file_document.php?idProject=".$idProject."'\">";

echo "<b>Document Type:</b> ";
echo "<select name='docType' style='height: 24px;width:450px;'>";
// GENERAL
echo "<option value='TN'>GENERAL - Technical Note (TN)</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// RB ... Requirement Baseline
echo "<option value='SSS'>RB - Software system specification (SSS)</option>";
echo "<option value='IRD'>RB - Interface requirements document (IRD)</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// TS ... Technical Specification
echo "<option value='SRS'>TS - Software requirements specification (SRS)</option>";
echo "<option value='ICD'>TS - Software interface control document (ICD)</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// DDF ... Design Definition File
echo "<option value='SSD'>DDF - Software design document (SDD)</option>";
echo "<option value='SCF'>DDF - Software configuration file (SCF)</option>";
echo "<option value='SRelD'>DDF - Software release document (SRelD)</option>";
echo "<option value='SUM'>DDF - Software user manual (SUM)</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// DJF ... Design Justification File
echo "<option value='SVerP'>DJF - Software verification plan (SVerP)</option>";
echo "<option value='SValP'>DJF - Software validation plan (SValP)</option>";
echo "<option value='SUITP'>DJF - Software integration test plan (SUITP)</option>";
echo "<option value='SUITP'>DJF - Software unit test plan (SUITP)</option>";
echo "<option value='SVS'>DJF - Software validation specification (SVS) with respect to TS</option>";
echo "<option value='SVS'>DJF - Software validation specification (SVS) with respect to RB</option>";
echo "<option value='TP'>DJF - Acceptance test plan</option>";
echo "<option value='TR'>DJF - Software unit test report</option>";
echo "<option value='TR'>DJF - Software integration test report</option>";
echo "<option value='SValR'>DJF - Software validation report with respect to TS</option>";
echo "<option value='SValR'>DJF - Software validation report with respect to RB</option>";
echo "<option value='TR'>DJF - Acceptance test report</option>";
echo "<option value='IR'>DJF - Installation report</option>";
echo "<option value='SVR'>DJF - Software verification report (SVR)</option>";
echo "<option value='SVerValR'>DJF - Independent software verification & validation report</option>";
echo "<option value='SRF'>DJF - Software reuse file (SRF)</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// MGT ... Management File
echo "<option value='SDP'>MGT - Software development plan (SDP)</option>";
echo "<option value='SRevP'>MGT - Software review plan (SRevP)</option>";
echo "<option value='SMP'>MGT - Software configuration management plan</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// MF ... Maintenance File
echo "<option value='MP'>MF - Maintenance plan</option>";
echo "<option value='MRec'>MF - Maintenance records</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// OP ... Operational
echo "<option value='OSP'>OP - Software operation support plan</option>";
echo "<option value='OTRes'>OP - Operational testing results</option>";
echo "<optgroup class=\"divider divider--blue\"></optgroup>";
// PAF ... Product Assurance
echo "<option value='SPAP'>PAF - Software product assurance plan (SPAP)</option>";
echo "<option value='SPAMR'>PAF - Software product assurance milestone report (SPAMR)</option>";
echo "</select><br/><br/>";

/*

CHEOPS Instrument Flight SW – Test Report for v1.0	CHEOPS-PNP-INST-TR-008_i1_IfswTestRepV1_0.pdf	A. Pasetti	1	24 November 2017	active
CHEOPS  Instrument Boot Software – Dry Run of RFT for ADS Tests	CHEOPS-PNP-INST-TR-007_i1_DryRunRFT.pdf	A. Pasetti	1	4 September 2017	active
CHEOPS  Instrument Boot Software – Test Report for v1.2	CHEOPS-PNP-INST-TR-006_i1_1_DbsTestRepV1_2.pdf	A. Pasetti	1,1	2 September 2017	active
	CHEOPS-PNP-INST-TR-006_i1_DbsTestRepV1_2.pdf	A. Pasetti	1	10 March 2017	superseded
CHEOPS Instrument Flight Software – Software-Level Test Report for EMC Tests	CHEOPS-PNP-INST-TR-005_i1_EmcTests.pdf	A. Pasetti	1	27 February 2017	active
CHEOPS Instrument Boot Software – Test Report for v1.1	CHEOPS-PNP-INST-TR-004_i1_DbsTestRepV1_0.pdf	A. Pasetti	1	22 December 2016	active
CHEOPS Instrument Flight SW – Test Report for v0.9+	CHEOPS-PNP-INST-TR-003_i1_IfswTestRepV0_9p.pdf	A. Pasetti	1	7 October 2016	active
CHEOPS Instrument Boot Software – Test Report for v1.0	CHEOPS-PNP-INST-TR-002_i1_DbsTestRepV1_0.pdf	A. Pasetti	1	28 July 2016	active
CHEOPS Instrument Flight SW – Test Report for v0.9-	CHEOPS-PNP-INST-TR-001_i1_IfswTestRepV0_9m.pdf	A. Pasetti	1	31 March 2016	active
CHEOPS Instrument Application SW – Specification	CHEOPS-PNP-INST-RS-001_i8_1_IaswSpec.pdf	A. Pasetti	8,1	31 October 2017	active
	CHEOPS-PNP-INST-RS-001_i8_IaswSpec.pdf	A. Pasetti	8	2 June 2017	superseded
	CHEOPS-PNP-INST-RS-001_i7_1_IaswSpec.pdf	A. Pasetti	7,1	25 July 2016	superseded
	CHEOPS-PNP-INST-RS-001_i7_IaswSpec.pdf	A. Pasetti	7	3 June 2016	superseded
	CHEOPS-PNP-INST-RS-001_i6_4_IaswSpec.pdf	A. Pasetti	6,4	31 March 2016	superseded
	CHEOPS-PNP-INST-RS-001_i6_3_IaswSpec.pdf	A. Pasetti	6,3	12 February 2016	superseded
	CHEOPS-PNP-INST-RS-001_i6_2_IaswSpec.pdf	A. Pasetti	6,2	11 January 2016	superseded
	CHEOPS-PNP-INST-RS-001_i6_1_IaswSpec.pdf	A. Pasetti	6,1	23 december 2015	superseded
	CHEOPS-PNP-INST-RS-001_i6_IaswSpec.pdf	A. Pasetti	6	23 October 2015	superseded
	CHEOPS-PNP-INST-RS-001_i5_IaswSpec.pdf	A. Pasetti	5	21 August 2015	superseded
	CHEOPS-PNP-INST-RS-001_i4_1_IaswSpec.pdf	A. Pasetti	4,1	20 July 2015	superseded
	CHEOPS-PNP-INST-RS-001_i4_IaswSpec.pdf	A. Pasetti	4	19 Jun 2015	superseded
	CHEOPS-PNP-INST-RS-001_i3_IaswSpec.pdf	A. Pasetti	3	20 Feb 2015	superseded
	CHEOPS-PNP-INST-RS-001_i2_IaswSpec.pdf	A. Pasetti	2		superseded
	CHEOPS-PNP-INST-RS-001_i1_IaswSpec.pdf	A. Pasetti	1		superseded
	CHEOPS-PNP-INST-RS-001_i0_1_IaswSpec.pdf	A. Pasetti	0,1		superseded
CHEOPS Instrument Flight SW - ECSS-E-ST-40C Tailoring	CHEOPS-PNP-INST-RS-002_i1_2_EcssE40Tailoring.pdf	A. Pasetti	1,2	20 Feb 2015	active
	CHEOPS-PNP-INST-RS-002_i1_1_EcssE40Tailoring.pdf	A. Pasetti	1,1		superseded
	CHEOPS-PNP-INST-RS-002_i1_EcssE40Tailoring.pdf	A. Pasetti	1		superseded
CHEOPS Instrument Boot SW – Specification	CHEOPS-PNP-INST-RS-004_i6_BootSwSpec.pdf	A. Pasetti	6	2 December 2016	active
	CHEOPS-PNP-INST-RS-004_i5_3_BootSwSpec.pdf	A. Pasetti	5,3	31 March 2016	superseded
	CHEOPS-PNP-INST-RS-004_i5_2_BootSwSpec.pdf	A. Pasetti	5,2	12 February 2016	superseded
	CHEOPS-PNP-INST-RS-004_i5_1_BootSwSpec.pdf	A. Pasetti	5,1	23 december 2015	superseded
	CHEOPS-PNP-INST-RS-004_i5_BootSwSpec.pdf	A. Pasetti	5	23 October 2015	superseded
	CHEOPS-PNP-INST-RS-004_i4_BootSwSpec.pdf	A. Pasetti	4	19 Jun2015	superseded
	CHEOPS-PNP-INST-RS-004_i3_BootSwSpec.pdf	A. Pasetti	3	20 Feb 2015	superseded
	CHEOPS-PNP-INST-RS-004_i2_BootSwSpec.pdf	A. Pasetti	2		superseded
	CHEOPS-PNP-INST-RS-004_i1_2_BootSwSpec.pdf	A. Pasetti	1,2		superseded
	CHEOPS-PNP-INST-RS-004_i1_1_BootSwSpec.pdf	A. Pasetti	1,1		superseded
	CHEOPS-PNP-INST-RS-004_i1_BootSwSpec.pdf	A. Pasetti	1		superseded
CHEOPS Instrument Flight SW – Specification	CHEOPS-PNP-INST-RS-005_i7_IfswSpec.pdf	A. Pasetti	7	31 October 2017	active
	CHEOPS-PNP-INST-RS-005_i6_IfswSpec.pdf	A. Pasetti	6	2 June 2017	superseded
	CHEOPS-PNP-INST-RS-005_i5_IfswSpec.pdf	A. Pasetti	5	23 September 2016	superseded
	CHEOPS-PNP-INST-RS-005_i4_3_IfswSpec.pdf	A. Pasetti	4,3	31 March 2016	superseded
	CHEOPS-PNP-INST-RS-005_i4_2_IfswSpec.pdf	A. Pasetti	4,2	12 February 2016	superseded
	CHEOPS-PNP-INST-RS-005_i4_1_IfswSpec.pdf	A. Pasetti	4,1	23 december 2015	superseded
	CHEOPS-PNP-INST-RS-005_i4_IfswSpec.pdf	A. Pasetti	4	23 October 2015	superseded
	CHEOPS-PNP-INST-RS-005_i3_IfswSpec.pdf	A. Pasetti	3	19 Jun 2015	superseded
	CHEOPS-PNP-INST-RS-005_i2_IfswSpec.pdf	A. Pasetti	1	20 Feb 2015	superseded
	CHEOPS-PNP-INST-RS-005_i1_IfswSpec.pdf	A. Pasetti	2		superseded
	CHEOPS-PNP-INST-RS-005_i0_1_IfswSpec.pdf	A. Pasetti	2		superseded
CHEOPS Instrument Basic SW – Specification	CHEOPS-PNP-INST-RS-006_i7_IbswSpec.pdf	A. Pasetti	7	2 June 2017	active
	CHEOPS-PNP-INST-RS-006_i6_IbswSpec.pdf	A. Pasetti	6	18 July 2016	superseded
	CHEOPS-PNP-INST-RS-006_i5_4_IbswSpec.pdf	A. Pasetti	5,4	31 March 2016	superseded
	CHEOPS-PNP-INST-RS-006_i5_3_IbswSpec.pdf	A. Pasetti	5,3	12 February 2016	superseded
	CHEOPS-PNP-INST-RS-006_i5_2_IbswSpec.pdf	A. Pasetti	5,2	11 January 2016	superseded
	CHEOPS-PNP-INST-RS-006_i5_1_IbswSpec.pdf	A. Pasetti	5,1	23 december 2015	superseded
	CHEOPS-PNP-INST-RS-006_i5_IbswSpec.pdf	A. Pasetti	5	23 October 2015	superseded
	CHEOPS-PNP-INST-RS-006_i4_IbswSpec.pdf	A. Pasetti	4	19 Jun2015	superseded
	CHEOPS-PNP-INST-RS-006_i3_IbswSpec.pdf	A. Pasetti	3	20 Feb 2015	superseded
	CHEOPS-PNP-INST-RS-006_i2_IbswSpec.pdf	A. Pasetti	2		superseded
	CHEOPS-PNP-INST-RS-006_i1_IbswSpec.pdf	A. Pasetti	1		superseded
CHEOPS Instrument Flight SW – Test Definition	CHEOPS-PNP-INST-RS-007_i1_2_IfswTestSpec	A. Pasetti	1,1	31 October 2017	active
	CHEOPS-PNP-INST-RS-007_i1_1_IfswTestSpec	A. Pasetti	1,1	26 June 2017	superseded
	CHEOPS-PNP-INST-RS-007_i1_0_IfswTestSpec	A. Pasetti	1	10 May 2017	superseded
	CHEOPS-PNP-INST-RS-007_i0_9_IfswTestSpec	A. Pasetti	0,9	23 September 2016	superseded
	CHEOPS-PNP-INST-RS-007_i0_8_IfswTestSpec	A. Pasetti	0,8	27 June 2016	superseded
	CHEOPS-PNP-INST-RS-007_i0_7_IfswTestSpec	A. Pasetti	0,7	31 March 2016	superseded
	CHEOPS-PNP-INST-RS-007_i0_6_IfswTestSpec	A. Pasetti	0,6	12 February 2016	superseded
	CHEOPS-PNP-INST-RS-007_i0_5_IfswTestSpec	A. Pasetti	0,5	23 January 2016	superseded
	CHEOPS-PNP-INST-RS-007_i0_4_IfswTestSpec	A. Pasetti	0,4	23 december 2015	superseded
	CHEOPS-PNP-INST-RS-007_i0_3_IfswTestSpec	A. Pasetti	0,3	21 August 2015	superseded
	CHEOPS-PNP-INST-RS-007_i0_2_IfswTestSpec	A. Pasetti	0,2	1 Jul 2015	superseded
	CHEOPS-PNP-INST-RS-007_i0_1_IfswTestSpec	A. Pasetti	0,1		superseded
CHEOPS Instrument Flight Software – PA Plan	CHEOPS-PNP-INST-PL-001_i1_IfswSpap.pdf	A. Pasetti	1	20 Feb 2015	active
	CHEOPS-PNP-INST-PL-001_i0_1_IfswSpap.pdf	A. Pasetti	0,1		superseded
CHEOPS Instrument Flight SW – Test Plan	CHEOPS-PNP-INST-PL-002_i3_2_IfswTestPlan.pdf	A. Pasetti	3,2	23 december 2015	active
	CHEOPS-PNP-INST-PL-002_i3_1_IfswTestPlan.pdf	A. Pasetti	3,1	1 Jul 2015	superseded
	CHEOPS-PNP-INST-PL-002_i3_IfswTestPlan.pdf	A. Pasetti	3	19 Jun 2015	superseded
	CHEOPS-PNP-INST-PL-002_i2_IfswTestPlan.pdf	A. Pasetti	2	20 Feb 2015	superseded
	CHEOPS-PNP-INST-PL-002_i1_IfswTestPlan.pdf	A. Pasetti	1		superseded
CHEOPS Instrument Application SW- TM/TC ICD -	CHEOPS-PNP-INST-ICD-001_i7_TmTcIcd.pdf	V.Cechticky	7	29 November 2017	active
	CHEOPS-PNP-INST-ICD-001_i6_TmTcIcd.pdf	V.Cechticky	6	18 March 2017	superseded
	CHEOPS-PNP-INST-ICD-001_i5_TmTcIcd.pdf	V.Cechticky	5	25 July 2016	superseded
	CHEOPS-PNP-INST-ICD-001_i4_1_TmTcIcd.pdf	V.Cechticky	4,1	8 January 2016	superseded
	CHEOPS-PNP-INST-ICD-001_i4_TmTcIcd.pdf	V.Cechticky	4	30 december 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i3_1_TmTcIcd.pdf	V.Cechticky	3,1	18 September 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i3_TmTcIcd.pdf	V.Cechticky	3	21 August 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i2_2_TmTcIcd.pdf	V.Cechticky	2,2	20 July 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i2_1_TmTcIcd.pdf	V.Cechticky	2,1	22 Jun 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i2_0_TmTcIcd.pdf	V.Cechticky	2	19 Jun 2015	superseded
	CHEOPS-PNP-INST-ICD-001_i1_1_IaswTmTcIcd.pdf	V.Cechticky	1,1		superseded
	CHEOPS-PNP-INST-ICD-001_i1_0_IaswTmTcIcd.pdf	V.Cechticky	1		superseded
	CHEOPS-PNP-INST-ICD-001_i0_2_IaswTmTcIcd.pdf	V.Cechticky	0,2		superseded
	CHEOPS-PNP-INST-ICD-001_i0_1_IaswTmTcIcd.pdf	V.Cechticky	0,1		superseded
CHEOPS Instrument Flight SW – Configuration File	CHEOPS-PNP-INST-ICD-002_i7_IfswConfigFile.pdf	V. Cecticky	7	29 November 2017	active
	CHEOPS-PNP-INST-ICD-002_i6_IfswConfigFile.pdf	V. Cecticky	6	18 March 2017	superseded
	CHEOPS-PNP-INST-ICD-002_i5_IfswConfigFile.pdf	V. Cecticky	5	25 July 2016	superseded
	CHEOPS-PNP-INST-ICD-002_i4_1_IfswConfigFile.pdf	V. Cecticky	4,1	8 January 2016	superseded
	CHEOPS-PNP-INST-ICD-002_i4_IfswConfigFile.pdf	V. Cecticky	4	23 december 2015	superseded
	CHEOPS-PNP-INST-ICD-002_i3_IfswConfigFile.pdf	V. Cecticky	3	21 August 2015	superseded
	CHEOPS-PNP-INST-ICD-002_i2_2_IfswConfigFile.pdf	V. Cecticky	2,2	20 July 2015	superseded
	CHEOPS-PNP-INST-ICD-002_i2_1_IfswConfigFile.pdf	V. Cecticky	2,1	22 Jun 2015	superseded
	CHEOPS-PNP-INST-ICD-002_i2_IfswConfigFile.pdf	V. Cecticky	2	19 Jun 2015	superseded
	CHEOPS-PNP-INST-ICD-002_i1_IfswConfigFile.pdf	V. Cecticky	1		superseded
CHEOPS Instrument Application SW – Architecture	CHEOPS-PNP-INST-DD-001_i5_IaswArch.pdf	A. Pasetti	5	8 November 2017	active
	CHEOPS-PNP-INST-DD-001_i4_IaswArch.pdf	A. Pasetti	4	16 July 2016	superseded
	CHEOPS-PNP-INST-DD-001_i3_3_IaswArch.pdf	A. Pasetti	3,2	12 February 2016	superseded
	CHEOPS-PNP-INST-DD-001_i3_2_IaswArch.pdf	A. Pasetti	3,2	11 January 2016	superseded
	CHEOPS-PNP-INST-DD-001_i3_1_IaswArch.pdf	A. Pasetti	3,1	23 december 2015	superseded
	CHEOPS-PNP-INST-DD-001_i2_2_IaswArch.pdf	A. Pasetti	2,2	21 August 2015	superseded
	CHEOPS-PNP-INST-DD-001_i2_1_IaswArch.pdf	A. Pasetti	2,1	20 Jul 2015	superseded
	CHEOPS-PNP-INST-DD-001_i2_IaswArch.pdf	A. Pasetti	2	19 Jun 2015	superseded
	CHEOPS-PNP-INST-DD-001_i1_IaswArch.pdf	A. Pasetti	1		superseded
CHEOPS Instrument Flight SW – Quality Report	CHEOPS-PNP-INST-RP-001_i3_QualityReport.pdf	A. Pasetti	3	7 October 2016	active
	CHEOPS-PNP-INST-RP-001_i2_QualityReport.pdf	A. Pasetti	2	29 March 2016	superseded
	CHEOPS-PNP-INST-RP-001_i1_1_QualityReport.pdf	A. Pasetti	1,1	8 February 2016	superseded
	CHEOPS-PNP-INST-RP-001_i1_0_QualityReport.pdf	A. Pasetti	1	23 december 2015	superseded
CHEOPS Instrument Flight SW – Test Coverage Report	CHEOPS-PNP-INST-RP-003_i2_CoverageRep.pdf	A. Pasetti	2	11 November 2017	active
	CHEOPS-PNP-INST-RP-003_i1_CoverageRep.pdf	A. Pasetti	1	17 June 2017	superseded
CHEOPS Instrument Flight SW – Inputs to FMEA	CHEOPS-PNP-INST-RP-002_i1_0_FmeaInputs.pdf	A. Pasetti	1	16 September 2017	active
	CHEOPS-PNP-INST-RP-002_i0_2_FmeaInputs.pdf	A. Pasetti	0,2	2 February 2017	superseded
	CHEOPS-PNP-INST-RP-002_i0_1_FmeaInputs.pdf	A. Pasetti	0,1	13 January 2017	superseded
CHEOPS Instrument Application SW – Automatic Code Generators	CHEOPS-PNP-INST-DD-002_i2_AutoCodeGen.pdf	V.Cechticky	2	23 September 2016	active
	CHEOPS-PNP-INST-DD-002_i1_AutoCodeGen.pdf	V.Cechticky	1	20 Feb 2015	superseded
CHEOPS Instrument Application SW – Configuration Code Generator	CHEOPS-PNP-INST-DD-003_i1_IaswAutoCodeGen.pdf	V.Cechticky	1	16 Mar 2015	active
CHEOPS Instrument Flight Software – Scheduler Design	CHEOPS-PNP-INST-DD-004_i2_IfswScheduling	A. Pasetti	2	10 June 2016	active
	CHEOPS-PNP-INST-DD-004_i1_1_IfswScheduling	A. Pasetti	1,1	11 January 2016	superseded
	CHEOPS-PNP-INST-DD-004_i1_IfswScheduling	A. Pasetti	1	19 Jun2015	superseded
	CHEOPS-PNP-INST-DD-004_i0_3_IfswScheduling	A. Pasetti	0,3		superseded
	CHEOPS-PNP-INST-DD-004_i0_2_IfswScheduling	A. Pasetti	0,2		superseded
	CHEOPS-PNP-INST-DD-004_i0_1_IfswScheduling	A. Pasetti	0,1		superseded
CHEOPS Instrument Application SW – Data Pool Component	CHEOPS-PNP-INST-DD-005_i1_1_DataPoolComponent.pdf	V.Cechticky	1,1	21 Apr 2015	active
	CHEOPS-PNP-INST-DD-005_i1_DataPoolComponent.pdf	V.Cechticky	1	21 Apr 2015	superseded
CHEOPS Instrument Application SW – User Manual	CHEOPS-PNP-INST-MAN-001_i1_0_IaswUserManual.pdf	A. Pasetti	1	23 September 2016	active
CHEOPS Instrument Flight SW – Command Sequences	CHEOPS-PNP-INST-MAN-002_i2_0_IfswCmdSeq.pdf	A. Pasetti	2	3 February 2017	active
	CHEOPS-PNP-INST-MAN-002_i1_1_IfswCmdSeq.pdf	A. Pasetti	1,1	23 September 2016	superseded
	CHEOPS-PNP-INST-MAN-002_i1_0_IfswCmdSeq.pdf	A. Pasetti	1	15 Jun 2016	superseded
	CHEOPS-PNP-INST-MAN-002_i0_3_IfswCmdSeq.pdf	A. Pasetti	0,3	28 May 2016	superseded
	CHEOPS-PNP-INST-MAN-002_i0_2_IfswCmdSeq.pdf	A. Pasetti	0,2	17 May 2016	superseded

*/



/*
$sql = "SELECT * FROM `component` ORDER BY `id`";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_component_editor.php?id=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>&nbsp;&nbsp;&nbsp;".substr($row["setting"],0,50);
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}
*/
?>

<b>Document Title:</b> <input type="text" name='title' style='height: 24px;width:450px;' value="" required /><br/><br/>

Number&nbsp;&nbsp;<input type="text" name='number' style='height: 24px;' value="001" required /><br/>

Version&nbsp;&nbsp;&nbsp;<input type="text" name='version1'  style='height: 24px;' value="0.1" required />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Version&nbsp;&nbsp;&nbsp;<input type="text" name='version2' style='height: 24px;' value="0.2" /><br/>

Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='date' style='height: 24px;' value="<?php echo date("d.m.Y"); ?>" required /><br/>

<br/>

<?php

echo "<input type='hidden' name='createInitialDoc' value='1'>";

echo "<a href=''><button style='width:200px;' name='CDV'>Create Document Version</button></a>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
"&nbsp;&nbsp;";

echo "<a href=''><button style='width:200px;' name='CRD'>Create Red-marked Doc.</button></a>";


echo "<br/>".$messageMngtDoc;

echo "</form>";

echo "<br/>";

echo "<form method=\"post\" enctype=\"multipart/form-data\" style=\"background-color: #d1d1d1; padding: 15px;\" onsubmit=\"this.action='sel_project-documentation.php?idProject=".$idProject."'\">";

echo "<input type='hidden' name='createInitialDoc' value='1'>";

echo "<a href='write_file_glossary.php?idProject=".$idProject."'><button style='width:200px;' name='CAL'>Create Acronym List (tex)</button></a>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "<a href='write_file_bibliography.php?idProject=".$idProject."'><button style='width:200px;' name='CRL'>Create Reference List (bib)</button></a>";

echo "<br/>".$messageCreateRL;

echo "</form>";

echo "</div>";

?>

<br/>

<h2>Existing Documents for this Project</h2>

<h3>Internal Documents</h3>

<?php

/* get all documents of this project */
$sql =
  "SELECT d.id as id, d.* FROM `projectdocument` AS pd, `document` AS d WHERE pd.idDocument = d.id AND idDocRelation = 1 AND pd.idProject = ".$idProject;
  //"SELECT * FROM document WHERE `idProject` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div class='collapsible' style='height:24px; padding:2px; margin-bottom: 2px; width:50%; background-color:LightSteelBlue;'>";
        echo "<a href='view_document.php?idProject=".$idProject."' >".$row["id"]." <b>".$docPrefix."-".$row["shortName"]."-".$row["number"]." / ".$row["name"]."</b></a>";
        echo "<span style='float:right;'>[>>]&nbsp;</span></div>";

        /* ################################################################################################### */
        /* get all versions of this document */

        $sql_sub =
          "SELECT * FROM `docversion` WHERE `idDocument` = ".$row['id']." ORDER BY `version` ASC";  

        $result_sub = $mysqli->query($sql_sub);

        echo "<div class='content'>";

        while($row_sub = $result_sub->fetch_assoc()) {
            echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:45%; background-color:#dbe4f0;margin-left:40px;'>";
            if ($row_sub["filename"] == "") {
                echo "<a href='view_document-version.php?idProject=".$idProject."&idDocument=".$row["id"]."&idVersion=".$row_sub["id"]."' >".$row_sub["version"].", ".$row_sub['date']."</a>";
            } else {
                echo "<a href='view_document-version.php?idProject=".$idProject."&idDocument=".$row["id"]."&idVersion=".$row_sub["id"]."' >".$row_sub["version"].", ".$row_sub['date']."</a> &nbsp;&nbsp;&nbsp; <img src='img/pdf.png' />&nbsp;<a href='docs/".strtolower($project_name)."/".$row_sub["filename"]."' target='_blank'>".substr($row_sub["filename"], 0, -4)."</a>";
            }
            
            $sql_pack = "SELECT pd.id as pid, pd.name FROM `projectDataPack` AS pd, `docDataPack` AS dd WHERE pd.id = dd.idDataPack AND dd.idDocVersion = ".$row_sub["id"];
            $result_pack = $mysqli->query($sql_pack);
            if (mysqli_num_rows($result_pack)>0) {
                while($row_pack = $result_pack->fetch_assoc()) {
                    //$row_pack = $result_pack->fetch_assoc();
                    $color = getColorFromString($row_pack['name']);
                    echo "<span style='float:right;background-color:".$color.";margin-right:2px;'>&nbsp;&nbsp;<a href='view_dataPack-document.php?idProject=".$idProject."&idDataPack=".$row_pack['pid']."' style='color:white;'>".$row_pack['name']."</a>&nbsp;&nbsp;</span>";
                }
            }
            
            echo "</div>";
        }

        echo "</div>";

        /* ################################################################################################### */

    }
} else {
    echo "0 results";
}

?>

<h3>External Documents</h3>

<?php

/* get all documents of this project */
$sql =
  "SELECT d.id as id, d.* FROM `projectdocument` AS pd, `document` AS d WHERE pd.idDocument = d.id AND idDocRelation = 0 AND pd.idProject = ".$idProject;
  //"SELECT * FROM document WHERE `idProject` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

//echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // get identifier
        $sql_identifier = "SELECT * FROM `docversion` WHERE idDocument = ".$row["id"];
        $result_identifier = $mysqli->query($sql_identifier);
        $row_identifier = $result_identifier->fetch_assoc();
        
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div class='collapsible' style='height:24px; padding:2px; margin-bottom: 2px; width:50%; background-color:LightSteelBlue;'>";
        echo "<a href='view_document.php?idProject=".$idProject."' >".$row["id"]." <b>".$row_identifier["identifier"]." / ".$row["name"]."</b></a>";
        echo "<span style='float:right;'>[>>]&nbsp;</span></div>";

        /* ################################################################################################### */
        /* get all versions of this document */

        $sql_sub =
          "SELECT * FROM `docversion` WHERE `idDocument` = ".$row['id']." ORDER BY `version` ASC";  

        $result_sub = $mysqli->query($sql_sub);

        echo "<div class='content'>";

        while($row_sub = $result_sub->fetch_assoc()) {
            echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:45%; background-color:#dbe4f0;margin-left:40px;'>";
            if ($row_sub["filename"] == "") {
                echo "<a href='view_document-version.php?idProject=".$idProject."&idDocument=".$row["id"]."&idVersion=".$row_sub["id"]."' >".$row_sub["version"].", ".$row_sub['date']."</a>";
            } else {
                echo "<a href='view_document-version.php?idProject=".$idProject."&idDocument=".$row["id"]."&idVersion=".$row_sub["id"]."' >".$row_sub["version"].", ".$row_sub['date']."</a> &nbsp;&nbsp;&nbsp; <img src='img/pdf.png' />&nbsp;<a href='docs/".strtolower($project_name)."/".$row_sub["filename"]."' target='_blank'>".substr($row_sub["filename"], 0, -4)."</a>";
            }
            
            $sql_pack = "SELECT pd.id as pid, pd.name FROM `projectDataPack` AS pd, `docDataPack` AS dd WHERE pd.id = dd.idDataPack AND dd.idDocVersion = ".$row_sub["id"];
            $result_pack = $mysqli->query($sql_pack);
            if (mysqli_num_rows($result_pack)>0) {
                while($row_pack = $result_pack->fetch_assoc()) {
                    //$row_pack = $result_pack->fetch_assoc();
                    echo "<span style='float:right;background-color:green;margin-right:2px;'>&nbsp;&nbsp;<a href='view_dataPack-document.php?idProject=".$idProject."&idDataPack=".$row_pack['pid']."' style='color:white;'>".$row_pack['name']."</a>&nbsp;&nbsp;</span>";
                }
            }
            
            echo "</div>";
        }

        echo "</div>";

        /* ################################################################################################### */

    }
} else {
    echo "0 results";
}

?>

<br/><br/>

<div style="font-size: x-small; line-height: 120%; " class="w3-container w3-pale-blue w3-leftbar w3-border-blue">

<?php
//echo "<font size='-4'>";
echo "<b>Legend</b>:<br/>";
echo "&nbsp;&nbsp;&nbsp;RB ... Requirement Baseline<br/>";
echo "&nbsp;&nbsp;&nbsp;TS ... Technical Specification<br/>";
echo "&nbsp;&nbsp;&nbsp;DDF ... Design Definition File<br/>";
echo "&nbsp;&nbsp;&nbsp;DJF ... Design Justification File<br/>";
echo "&nbsp;&nbsp;&nbsp;MGT ... Management File<br/>";
echo "&nbsp;&nbsp;&nbsp;MF ... Maintenance File<br/>";
echo "&nbsp;&nbsp;&nbsp;OP ... Operational<br/>";
echo "&nbsp;&nbsp;&nbsp;PAF ... Product Assurance<br/>";
//echo "</font>";
?>

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