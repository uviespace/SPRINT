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
    echo "0 results for projects";
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
	<script type="text/javascript">
		var url = "http://localhost/dbeditor/";
	</script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
	<style type="text/css">

	</style>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Project <?php echo $project_name;?> - Document Management</h2>
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

<?php

echo "<div>";

echo "<b>Prefix:</b> <input type='text' value='SXI-UVIE-INST' /><br/><br/>";

echo "<b>Package:</b> ";
echo "<select>";
echo "<option>SIR - Simulator Release</option>";
echo "<option>SRR - System Requirements Review</option>";
echo "<option>PDR - Preliminary Design Review</option>";
echo "<option>CDR - Critical Design Review</option>";
echo "<option>QAR - Qualification/Acceptance Review</option>";
echo "</select>";

echo "&nbsp;&nbsp;&nbsp;<a href=''><button style='width:200px;'>Create Package Item List</button></a>";


echo "<br/><br/>";

//ECSS-Q-ST-80C-6March2009

echo "<b>Document Type:</b> ";
echo "<select>";
// GENERAL
echo "<option>GENERAL - Technical Note (TN)</option>";
// RB ... Requirement Baseline
echo "<option>RB - Software system specification (SSS)</option>";
echo "<option>RB - Interface requirements document (IRD)</option>";
// TS ... Technical Specification
echo "<option>TS - Software requirements specification (SRS)</option>";
echo "<option>TS - Software interface control document (ICD)</option>";
// DDF ... Design Definition File
echo "<option>DDF - Software design document (SSD)</option>";
echo "<option>DDF - Software configuration file (SCF)</option>";
echo "<option>DDF - Software release document (SRelD)</option>";
echo "<option>DDF - Software user manual (SUM)</option>";
// DJF ... Design Justification File
echo "<option>DJF - Software verification plan (SVerP)</option>";
echo "<option>DJF - Software validation plan (SValP)</option>";
echo "<option>DJF - Software integration test plan (SUITP)</option>";
echo "<option>DJF - Software unit test plan (SUITP)</option>";
echo "<option>DJF - Software validation specification (SVS) with respect to TS</option>";
echo "<option>DJF - Software validation specification (SVS) with respect to RB</option>";
echo "<option>DJF - Acceptance test plan</option>";
echo "<option>DJF - Software unit test report</option>";
echo "<option>DJF - Software integration test report</option>";
echo "<option>DJF - Software validation report with respect to TS</option>";
echo "<option>DJF - Software validation report with respect to RB</option>";
echo "<option>DJF - Acceptance test report</option>";
echo "<option>DJF - Installation report</option>";
echo "<option>DJF - Software verification report (SVR)</option>";
echo "<option>DJF - Independent software verification & validation report</option>";
echo "<option>DJF - Software reuse file (SRF)</option>";
// MGT ... Management File
echo "<option>MGT - Software development plan (SDP)</option>";
echo "<option>MGT - Software review plan (SRevP)</option>";
echo "<option>MGT - Software configuration management plan</option>";
// MF ... Maintenance File
echo "<option>MF - Maintenance plan</option>";
echo "<option>MF - Maintenance records</option>";
// OP ... Operational
echo "<option>OP - Software operation support plan</option>";
echo "<option>OP - Operational testing results</option>";
// PAF ... Product Assurance
echo "<option>PAF - Software product assurance plan (SPAP)</option>";
echo "<option>PAF - Software product assurance milestone report (SPAMR)</option>";
echo "</select><br/><br/>";

echo "</div>";

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

<b>Document Title:</b> <input type="text" value="" /><br/><br/>

Number&nbsp;&nbsp;<input type="text" value="001" /><br/>

Version&nbsp;&nbsp;&nbsp;<input type="text" value="0.1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Version&nbsp;&nbsp;&nbsp;<input type="text" value="0.2" /><br/>

Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" value="<?php echo date("d.m.Y"); ?>" /><br/>

<br/>

<a href=""><button style="width:200px;">Create Initial Document</button></a>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;

<a href=""><button style="width:200px;">Create Red-marked Doc.</button></a><br/><br/>

<a href=""><button style="width:200px;">Create Reference List (bib)</button></a>

<br/>

<h2>Existing Documents for this Project</h2>

<?php

$sql = "SELECT * FROM `project` WHERE `id` = 1 ORDER BY `id` ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_component_editor.php?id=".$row["id"]."&idProject=".$idProject."'>".$row["id"]." <b>".$row["name"]."</b></a>&nbsp;&nbsp;&nbsp;".substr($row["setting"],0,50);
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}

?>

<br/><br/>

<div>

<?php
echo "<b>Legend</b>:<br/>";
echo "&nbsp;&nbsp;&nbsp;RB ... Requirement Baseline<br/>";
echo "&nbsp;&nbsp;&nbsp;TS ... Technical Specification<br/>";
echo "&nbsp;&nbsp;&nbsp;DDF ... Design Definition File<br/>";
echo "&nbsp;&nbsp;&nbsp;DJF ... Design Justification File<br/>";
echo "&nbsp;&nbsp;&nbsp;MGT ... Management File<br/>";
echo "&nbsp;&nbsp;&nbsp;MF ... Maintenance File<br/>";
echo "&nbsp;&nbsp;&nbsp;OP ... Operational<br/>";
echo "&nbsp;&nbsp;&nbsp;PAF ... Product Assurance<br/>";
?>

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
</body>

</html>