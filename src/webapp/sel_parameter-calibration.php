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
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
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

?>
<!DOCTYPE html>
<html>
<head>
	<title>Calibration Curves</title>
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

	</style>
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
		            <h2>Calibration Curves</h2>
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

<?php

echo "<a href='open_calibration_editor.php?id=0&idProject=".$idProject."&idStandard=".$idStandard."'>Create new Numerical Calibration Curve</a><br/>";
echo "<a href='open_calibration_editor.php?id=0&type=1&idProject=".$idProject."&idStandard=".$idStandard."'>Create new Polynomial Calibration Curve</a><br/>";
echo "<a href='open_calibration_editor.php?id=0&type=2&idProject=".$idProject."&idStandard=".$idStandard."'>Create new Logarithmical Calibration Curve</a><br/>";
echo "<br/>";

$sql = 
  "SELECT ".
  "* ".
  "FROM ".
  "`calibration` ".
  "WHERE ".
  "`idStandard` = ".$idStandard." ".
  "ORDER BY `name` ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Short Description: " . $row["shortDesc"]. "<br/>";
        
        $sql_nmb = "SELECT * FROM `parameter` p WHERE idStandard = ".$idStandard." AND JSON_VALUE(p.setting, '$.calcurve') = ".$row["id"];
        $result_nmb = $mysqli->query($sql_nmb);
        $num_rows_nmb = mysqli_num_rows($result_nmb);
        
        if ($num_rows_nmb > 0) {
            echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:75%; background-color:lightgreen;'>";
        } else {
            echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:75%; background-color:lightgray;'>";
        }
        echo $row["id"]." ";
        echo "<a href='open_calibration_editor.php?id=".$row["id"]."&type=".$row["type"]."&idProject=".$idProject."&idStandard=".$idStandard."'><b>".$row["name"]."</b></a>";
        echo " (".$row["shortDesc"].") ... ".json_decode($row["setting"])->unit;
        if ($row["type"] == "0") { $calType = "NUM";  $bgcolor = "yellow";
        } else if ($row["type"] == "1") { $calType = "POL"; $bgcolor = "orange";
        } else if ($row["type"] == "2") { $calType = "LOG"; $bgcolor = "#C4A484";
        } else { $calType = "n/a"; }
        echo "<span style='float: right;background-color: ".$bgcolor.";width: 40px;'>&nbsp;".$calType."&nbsp;</span>";
        echo "<span style='float: right;'>".$num_rows_nmb."&nbsp;</span>";
        echo "</div>";
    }
} else {
    echo "0 results";
}
        
echo "<br/>";
?>

<div>
<h2>Parameters with Calibration Curves</h2>
</div>

<?php


$sql = "SELECT DISTINCT t.*, JSON_VALUE(t.setting, '$.calcurve') as calcurve FROM `parameter` t WHERE JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') AND t.idStandard = ".$idStandard." ORDER BY t.domain, t.name";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        
        $sqlenum = 
          "SELECT ".
          "p.unit, (SELECT COUNT(*) FROM `enumeration` e WHERE e.idType = p.idType) AS ecnt ".
          "FROM ".
          "`parameter` p, `type` t ".
          "WHERE ".
          "p.idStandard = ".$idStandard." ".
          "AND ".
          "p.idType = t.id ".
          "AND ".
          "p.id = ".$row["id"]." ";
        $resultenum = $mysqli->query($sqlenum);
        $num_rowsenum = mysqli_num_rows($resultenum);
        $rowEnum = $resultenum->fetch_assoc();
        if ($rowEnum["ecnt"] > 0) {
            $color = "orange";
        } else {
            $color = "lightblue";
        }
        
        echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:75%; background-color:".$color.";'>";
        if ($row["kind"] < 3) { // parameter
            echo "<a href='view_parameter.php?idProject=".$idProject."&idStandard=".$idStandard."&idParameter=".$row["id"]."' style='padding:3px;background-color:#C1FFC1;'>".$row["id"]."</a>";
        } else { // datapool
            echo "<a href='view_datapool.php?idProject=".$idProject."&idStandard=".$idStandard."&idParameter=".$row["id"]."' style='padding:3px;background-color:#FFD39B;'>".$row["id"]."</a>";
        }
        echo "&nbsp;";
        echo "<a href='view_parameter-calibration.php?idProject=".$idProject."&idStandard=".$idStandard."&idParameter=".$row["id"]."' ><b>".$row["domain"]." / ".$row["name"]."</b>";
        echo "</a>";
        if ($rowEnum["unit"] != "") {
            echo " <font color=green>(".$rowEnum["unit"].")</font>";
        }
        
        $sqlCal = "SELECT `name` FROM `calibration` WHERE `idStandard` = ".$idStandard." AND `id` = ".$row["calcurve"];
        $resultCal = $mysqli->query($sqlCal);
        $rowCal = $resultCal->fetch_assoc();
        echo "<span style='float: right;'>".$row["calcurve"]." <b>".$rowCal["name"]."</b>&nbsp;</span>";
        
        echo "</div>";

    }
} else {
    echo "0 results";
}
?>

<div>
<h2>Parameters with no Calibration Curves</h2>
</div>

<?php

$sql = "SELECT DISTINCT t.* FROM `parameter` t WHERE (JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') IS NULL OR JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') = '' OR t.setting = '' OR t.setting = '{}') AND t.idStandard = ".$idStandard." ORDER BY t.domain, t.name";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:75%; background-color:lightblue;'>";
        echo "<a href='view_parameter-calibration.php?idProject=".$idProject."&idStandard=".$idStandard."&idParameter=".$row["id"]."' >".$row["id"]." <b>".$row["domain"]." / ".$row["name"]."</b></a>";
        
        $sqlenum = 
          "SELECT ".
          "(SELECT COUNT(*) FROM `enumeration` e WHERE e.idType = p.idType) AS ecnt ".
          "FROM ".
          "`parameter` p, `type` t ".
          "WHERE ".
          "p.idStandard = ".$idStandard." ".
          "AND ".
          "p.idType = t.id ".
          "AND ".
          "p.id = ".$row["id"]." ";
        $resultenum = $mysqli->query($sqlenum);
        $num_rowsenum = mysqli_num_rows($resultenum);
        $rowEnum = $resultenum->fetch_assoc();
        if ($rowEnum["ecnt"] > 0) {
            echo "<span style='float: right;background-color: orange;width:85px;'>&nbsp;Enums: ".$rowEnum["ecnt"]."&nbsp;</span>";
        }
        
        echo "</div>";
//        echo "<br/>";
    }
} else {
    echo "0 results";
}

?>

<br/><br/>
<br/><br/>
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

	</div>
</body>

</html>