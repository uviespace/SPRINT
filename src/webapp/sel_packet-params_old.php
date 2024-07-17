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
	<title>Packet Parameters</title>
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

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Packet Parameters</h2>
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

//$sql = "SELECT DISTINCT t.* FROM `type` t LEFT JOIN `enumeration` e ON t.id = e.idType WHERE  t.idStandard = ".$idStandard." AND e.idType IS NOT NULL ORDER BY e.value";
/**
$sql = 
  "SELECT DISTINCT ".
  "p.* ".
  "FROM ".
  "`packet` p ".
  "INNER JOIN ".
  "`packet` pa ".
  "ON ".
  "p.id = pa.idParent ".
  "WHERE ".
  "p.idStandard = ".$idStandard." ".
  "ORDER BY p.domain, p.name";
  */
$sql = 
  "SELECT DISTINCT ".
  "p.* ".
  "FROM ".
  "`packet` p ".
  "WHERE ".
  "p.idStandard = ".$idStandard." AND ".
  "(p.discriminant = '' OR ".
  "p.discriminant IS NULL) ".
  "ORDER BY p.domain, p.subtype";
  


$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        
        $idPacket = $row["id"];
        $sql_params = 
          "SELECT ".
          "ps.id, ps.type, ps.idParameter, ps.order, ps.role, ps.group, ps.repetition, ps.desc, p.name, p.idType, t.size ".
          "FROM `parametersequence` AS ps, `parameter` AS p, `type` AS t ".
          "WHERE ps.idPacket = ".$idPacket." AND ps.idParameter = p.id AND p.idType = t.id ".
          "ORDER BY ps.order ASC";
        $result_params = $mysqli->query($sql_params);
        $num_rows_params = mysqli_num_rows($result_params);
        
        echo "<div style='height:24px; padding:2px; margin-bottom: 2px; width:50%; background-color:lightblue;'>";
        if ($row["kind"] == 1) {
            echo "<a href='view_packet-params.php?idProject=".$idProject."&idStandard=".$idStandard."&idPacket=".$row["id"]."' >".$row["id"]." <b>".$row["domain"]." / TM(".$row["type"]."/".$row["subtype"].") ".$row["name"]."</b></a> <a href='packet-inspector.php?idProject=".$idProject."&idStandard=".$idStandard."&idPacket=".$row["id"]."'><img width='20px' src='img/datainspector-icon.png' /></a> ... <b>".$num_rows_params."</b> parameter(s)";
        } else {
            echo "<a href='view_packet-params.php?idProject=".$idProject."&idStandard=".$idStandard."&idPacket=".$row["id"]."' >".$row["id"]." <b>".$row["domain"]." / TC(".$row["type"]."/".$row["subtype"].") ".$row["name"]."</b></a> <a href='packet-inspector.php?idProject=".$idProject."&idStandard=".$idStandard."&idPacket=".$row["id"]."'><img width='20px' src='img/datainspector-icon.png' /></a> ... <b>".$num_rows_params."</b> parameter(s)";
        }
        echo "</div>";
    }
} else {
    echo "0 results";
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
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

				</div>

	</div>
</body>

</html>