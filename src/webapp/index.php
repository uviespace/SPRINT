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

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
//echo "Hallo User: ".$userid;

// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;

$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

$userName = $row["name"];

?>
<!DOCTYPE html>
<html>
<head>
	<title>SRINT</title>
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
.aho:hover {
    background-color: red;
    
}
	</style>
<!--
<style type="text/css">
 .topcorner{
   position:absolute;
    top:10px;
    right: 10px;
  }
  
   .topcorner_left{
   position:absolute;
    top:10px;
    left: 10px;
  }

	.logo  {
  background-color: black;
  color: white;
  margin: 20px;
  padding: 20px;
	}
	
	.offset {
	background-color: blue;
    width: 100%;
    height: 500px;
}
	</style>
-->
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Space Project Resource Integration Network Toolbox</h2>
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

        <div>
            The Space Project Resource Integration Network Tool integrates following standards, resources and processes:
            <ul>
                <li>ECSS (European Co-operation for Space Standardisation) Software Engineering standardization and processes:
                    <a href="https://ecss.nl/" target="_blank">ECSS</a>,
                    <a href="https://www.esa.int/esapub/bulletin/bullet111/chapter21_bul111.pdf" target="_blank">Introducing ECSS Software-Engineering Standards within ESA (ESA bulletin 111 — august 2002)</a>,
                </li>
                <li>SCOS2000 Mission Control System (e.g. run-time database): 
                    <a href="https://www.esa.int/Enabling_Support/Operations/Ground_Systems_Engineering/SCOS-2000" target="_blank">ESA - SCOS-2000</a>, 
                    <a href="https://issues.cosmos.esa.int/solarorbiterwiki/download/attachments/23757668/MOC-applicable%20MIB%20egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf?version=1&modificationDate=1522940882000&api=v2" target="_blank">MOC-applicable MIB egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf</a>
                </li>
                <li>EGS-CC (European Ground Systems Common Core) - common infrastructure to support space systems monitoring and control in pre- and post-launch phases for all mission types:
                    <a href="http://www.egscc.esa.int/" target="_blank">ESA - EGS-CC</a>
                <li>CORDET Framework from PnP Software including code generation functionality (see below)</li>
            </ul>
        </div>

        <div>
            <h3><img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="120" style="background-color: darkblue; padding: 5px;"> CORDET FW Editor</h3>
        </div>

		<div>
			The CORDET FW Editor is a web-based tool to support the specification of a PUS-based system communication 
			standard and of the applications which use it. The PUS 
			(<a href="http://www.ecss.nl/wp-content/uploads/standards/ecss-e/ECSS-E-70-41A30Jan2003.pdf" target="_blank">Packet Utilization Standard</a>) 
			is an interface 
			standard promoted by the European Space Agency for on-board applications.
			<br/>
			The CORDET FW Editor allows a user to enter the specification information for a PUS-based system and to 
			generate from it the following items:
			<ul>
				<li>An Interface Control Document (ICD)</li>
				<li>A C-language component which implements the data pool for the applications in the PUS system</li>
				<li>A set of tables which specify the telecommands and telemetry reports in the PUS system and which 
				can be imported in a specification document</li>
				<li>The configuration files to instantiate the <a href="https://www.pnp-software.com/cordetfw/" target="_blank">CORDET Framework</a>
				for the applications in the PUS system</li>
			</ul>
			The <img<a href="https://www.pnp-software.com/cordetfw/editor-1.1/_lib/libraries/grp/doc/UserManual.html" target="_blank">help pages</a>
			explains how to use the CORDET FW Editor. The editor is publicly accessible for 
			registered users. Registration is free and only requires the user to enter a valid e-mail address. Local 
			installations of the editor are available on a commercial basis from 
			<a href="https://www.pnp-software.com/" target="_blank">P&P Software GmbH</a>.
		</div>

		<div>
			<hr>
<?php
if ($userid == 1 || $userid == 1001) {
?>
			<a href="mng_user.php" target="_self">
				<button style="width:220px;text-align:left;">
					<img src="img/users_64x64.png" width="32" height="32">&nbsp;&nbsp;Manage Users...
				</button>
			</a>
			<br/>
			<hr>
<?php
}
?>
			<a href="mng_acronym.php" target="_self">
				<button style="width:220px;text-align:left;">
					<img src="img/acronym_1_64x64.png" width="32" height="32">&nbsp;&nbsp;Manage Acronyms...
				</button>
			</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a href="mng_reference.php" target="_self">
				<button style="width:220px;text-align:left;">
					<img src="img/reference_64x64.png" width="32" height="32">&nbsp;&nbsp;Manage References...
				</button>
			</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a href="mng_organisation.php" target="_self">
				<button style="width:220px;text-align:left;">
					<img src="img/org_1_64x64.png" width="32" height="32">&nbsp;&nbsp;Manage Organisations...
				</button>
			</a>
			<br/>
			<hr>
			<a href="mng_project.php" target="_self">
				<button style="width:220px;text-align:left;">
					<img src="img/projects_64x64.png" width="32" height="32">&nbsp;&nbsp;Manage My Projects...
				</button>
			</a>
			<br/>
			<br/>
			<a href="sel_project.php" target="_self">
				<button style="width:220px;text-align:left;">
					&nbsp;&nbsp;<img src="img/open_64x64.png" width="16" height="16">&nbsp;&nbsp;Open Projects...
				</button>
			</a>
			<br/>
			<hr>
			<div style="float:left;">(c) 2019-2021, University of Vienna</div>
            <div style="float:right;"><a href="">Impressum</a></div>
            <div style="float:right;"><a class="aho" href="mng_media.php" style="color:white;">&nbsp;M&nbsp;</a>&nbsp;&nbsp;</div>
            <br/>
			<hr>
			<br/>
		</div>

		<div class="topcorner_left">
<?php include 'logos.php'; ?>
			<br/><br/>
			You are logged in as: <br/>
			<?php 
			    echo "<b>".$userName."</b><br/>";
			    echo "<a href='profile.php'>Edit Profile</a>";
			?>
			<br/><br/>
			<a href="logout.php">Logout</a>
		<div/>
		
	</div>

</body>

</html>