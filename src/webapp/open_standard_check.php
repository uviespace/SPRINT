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
require 'int/global_functions.php';

if (isset($_GET["id"])) { $idComponent  = $_GET["id"]; } else { $idComponent=0; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
/*if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };*/
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };


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

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

$idRole = get_max_access_level($mysqli, $idProject, $userid, $userEmail);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Check </title>
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
    
    <!-- Foundation CSS framework (Bootstrap and jQueryUI also supported) -->
    <!--<link rel='stylesheet' href='//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.css'>-->
    <!-- Font Awesome icons (Bootstrap, Foundation, and jQueryUI also supported) -->
    <!-- Cheatsheet: http://btsai.github.io/font_awesome4_cheatsheet/index.html -->
    <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css'>

    <!--<link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css'>-->
    <!--<link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/themes/modern.min.css'>-->
    <script src='//cdn.jsdelivr.net/jquery/2.1.1/jquery.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/plugins/xhtml.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/json-editor/0.7.28/jsoneditor.min.js"></script>

    <script src='int/json_editor_standard.js'></script>
    
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
	<!--<script type="text/javascript" src="js/item-ajax.js"></script>-->
</head>
<body>
<a id="Top"></a>
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
		            <h2>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h2>
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


<h2>Standard Check</h2>

<!--<?php
$sql = "SELECT * FROM `standard` WHERE `id` = ".$idStandard." AND `idProject` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    echo "setting: " . $row["setting"]. "<br/><br/>";
} else {
    echo "0 results";
}
?>-->

<a href="#Parameter">Parameter</a><br/>
<a href="#Datatypes">Datatypes</a>

<hr>

<a id="Parameter"></a>
<h3>Parameter</h3>

<?php
$sql = 
  "SELECT ".
  "p.id, p.domain, p.name, t.name AS tname, (SELECT COUNT(*) FROM `enumeration` e WHERE e.idType = p.idType) AS ecnt, p.kind, p.idType, JSON_VALUE(p.setting, '$.calcurve') AS cc ".
  "FROM ".
  "`parameter` p, `type` t ".
  "WHERE ".
  "p.idStandard = ".$idStandard." ".
  "AND ".
  "p.idType = t.id ".
  "ORDER BY ".
  "`domain`, `name` ASC ";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);
echo $num_rows." Parameter found.<br/><br/>\n";

echo "<table id='tabcheck' border=1>\n";
echo "<thead style='font-weight: bold;'>\n";
echo "<tr><td>#</td><td>domain / name</td><td width='200px'>datatype</td><td width='100px'>calibration</td><td width='100px'>kind</td><td width='400px'>packet</td></tr>\n";
echo "</thead>\n";
echo "<tbody>\n";

if ($result->num_rows > 0) {
	$i = 0;
	while($row = $result->fetch_assoc()) {
		
		$sqlpckt = "SELECT * FROM `parametersequence` ps, `packet` p WHERE ps.idParameter = ".$row["id"]." AND ps.idPacket = p.id";
		$resultpckt = $mysqli->query($sqlpckt);
		$num_rows_pckt = mysqli_num_rows($resultpckt);
		$i += 1;
		
        echo "<tr>";
		echo "<td>" . $i . "</td>";  // #
		echo "<td>" . $row["domain"] . " / " . $row["name"] . "</td>";  // domain / name
		echo"<td>" . $row["tname"] . "</td>";
		if ($row["ecnt"] > 0) {  // enumeration
		    echo"<td bgcolor='#87CEFA'>";
			echo "<a href='view_type-enumeration.php?idProject=".$idProject."&idStandard=".$idStandard."&idType=".$row["idType"]."'>enum</a> ";
			echo $row["ecnt"];
			echo "</td>";
        } else if ($row["cc"] != '') {  // calibration curve
		    echo"<td bgcolor='lightgreen'>";
			echo "<a href='open_calibration_editor.php?id=".$row["cc"]."&idProject=".$idProject."&idStandard=".$idStandard."'>calcurv</a> ";
			echo $row["cc"];
			echo "</td>";
		} else {
			echo"<td></td>";
		}
		if ($row["kind"] > 2) {  // kind
		echo"<td bgcolor='#FFD39B'>DP " . $row["kind"] . "</td>";
		} else if ($row["kind"] == 2) {
		echo"<td bgcolor='#C1FFC1'>Par " . $row["kind"] . "</td>";
		} else {
		echo"<td bgcolor='#CFCFCF'>Hdr " . $row["kind"] . "</td>";
		}
		if ($num_rows_pckt > 0) {  // packet
			echo"<td bgcolor='#a799ff'>" . $num_rows_pckt . " (";
			while ($rowpckt = $resultpckt->fetch_assoc()) {
				if ($rowpckt["role"]==3) {  // discriminant
					$format = "<font color=orange>";
					$formatend = "</font>";
				} else if ($rowpckt["role"]==8) {  // spare
					$format = "<font color=white>";
					$formatend = "</font>";
				} else {
					$format = "";
					$formatend = "";
				}
			    if ($rowpckt["name"]=="") {
					
					$sqlparent = "SELECT * FROM `packet` p WHERE p.id = ".$rowpckt["idParent"];
		            $resultparent = $mysqli->query($sqlparent);
		            $num_rows_parent = mysqli_num_rows($resultparent);
					$rowparent = $resultparent->fetch_assoc();
					
					$format = "<a href='view_packet-derived.php?idProject=".$idProject."&idStandard=".$idStandard."&idParent=".$rowpckt["idParent"]."'>#".$rowparent["name"]."</a> ".$format;
					echo "<font color=red>derived:</font> ".$format.$rowpckt["discriminant"].$formatend." ";
				} else {
					echo $format.$rowpckt["name"].$formatend." ";
				}
			}
			echo")</td>";
		} else {
			echo"<td></td>";
		}
		echo"</tr>\n";
	}
} else {
    echo "0 results";
}

echo "</tbody>\n";
echo "</table>\n";

?>

<a href="#Top">Top</a><br/>

<hr>

<a id="Datatypes"></a>
<h3>Datatypes</h3>

<?php

$sqlpredef = 
  "SELECT ".
  "t.id, t.name, t.desc, t.nativeType, (SELECT COUNT(*) FROM `enumeration` e WHERE e.idType = t.id) AS ecnt ".
  "FROM ".
  "`type` t ".
  "WHERE ".
  "t.idStandard IS NULL ".
  "AND ".
  "t.id < 300 ".
  "ORDER BY ".
  "t.id ASC ";
  
  
$resultpredef = $mysqli->query($sqlpredef);

$num_rows_predef = mysqli_num_rows($resultpredef);
echo $num_rows_predef." Pre-defined Datatypes found.<br/>\n";


$sql = 
  "SELECT ".
  "t.id, t.name, t.desc, t.nativeType, (SELECT COUNT(*) FROM `enumeration` e WHERE e.idType = t.id) AS ecnt ".
  "FROM ".
  "`type` t ".
  "WHERE ".
  "t.idStandard = ".$idStandard." ".
  "ORDER BY ".
  "`name` ASC ";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);
echo $num_rows." Datatypes found.<br/><br/>\n";


echo "<table id='tabcheck' border=1>\n";
echo "<thead style='font-weight: bold;'>\n";
echo "<tr><td>#</td><td>name</td><td width='400px'>desc</td><td width='100px'>native type</td><td width='100px'>enumeration</td><td width='100px'>parameter</td></tr>\n";
echo "</thead>\n";
echo "<tbody>\n";

if ($resultpredef->num_rows > 0) {
	$i = 0;
	while($rowpredef = $resultpredef->fetch_assoc()) {
		
		$sqlparam = "SELECT * FROM `parameter` p WHERE p.idType = ".$rowpredef["id"]." AND p.idStandard = ".$idStandard;
		$resultparam = $mysqli->query($sqlparam);
		$num_rows_param = mysqli_num_rows($resultparam);
		$i += 1;
		
        echo "<tr>";
		echo "<td>" . $i . "</td>";  // #
		echo "<td bgcolor='#FFE4C4'>" . $rowpredef["name"] . "</td>";  // name
		echo "<td bgcolor='#FFE4C4'>" . $rowpredef["desc"] . "</td>";
		echo "<td>" . $rowpredef["nativeType"] . "</td>";
		if ($rowpredef["ecnt"] > 0) {  // enumeration
		    echo"<td bgcolor='#87CEFA'>";
			echo "<a href='view_type-enumeration.php?idProject=".$idProject."&idStandard=".$idStandard."&idType=".$rowpredef["id"]."'>enum</a> ";
			echo $rowpredef["ecnt"];
			echo "</td>";
		} else {
			echo"<td></td>";
		}
		if ($num_rows_param > 0) {  // parameter
		    echo"<td bgcolor='#CAFF70'>";
			echo $num_rows_param;
			echo "</td>";
		} else {
			echo"<td></td>";
		}
		echo "</tr>";
		
	}
} else {
    echo "0 results";
}


if ($result->num_rows > 0) {
	$i = 0;
	while($row = $result->fetch_assoc()) {
		
		$sqlparam = "SELECT * FROM `parameter` p WHERE p.idType = ".$row["id"];
		$resultparam = $mysqli->query($sqlparam);
		$num_rows_param = mysqli_num_rows($resultparam);
		$i += 1;
		
        echo "<tr>";
		echo "<td>" . $i . "</td>";  // #
		echo "<td>" . $row["name"] . "</td>";  // name
		echo "<td>" . $row["desc"] . "</td>";
		echo "<td>" . $row["nativeType"] . "</td>";
		if ($row["ecnt"] > 0) {  // enumeration
		    echo"<td bgcolor='#87CEFA'>";
			echo "<a href='view_type-enumeration.php?idProject=".$idProject."&idStandard=".$idStandard."&idType=".$row["id"]."'>enum</a> ";
			echo $row["ecnt"];
			echo "</td>";
		} else {
			echo"<td></td>";
		}
		if ($num_rows_param > 0) {  // parameter
		    echo"<td bgcolor='#CAFF70'>";
			echo $num_rows_param;
			echo "</td>";
		} else {
			echo"<td></td>";
		}
		echo "</tr>";
		
	}		
} else {
    echo "0 results";
}

echo "</tbody>\n";
echo "</table>\n";

?>

<a href="#Top">Top</a><br/>

<hr>

<br/><br/>



				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>



	</div>
</body>

</html>