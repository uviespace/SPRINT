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
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["sel_relation"])) { $relation  = $_GET["sel_relation"]; } else { $relation=1; };
$project_name = "";
$application_name = "";
$application_desc = "";

if (isset($_GET["add_relation"])) { addRel($mysqli, $idApplication, $idStandard, $relation); };
if (isset($_GET["chg_relation"])) { chgRel($mysqli, $idApplication, $idStandard, $relation); };
if (isset($_GET["del_relation"])) { delRel($mysqli, $idApplication, $idStandard, $relation); };

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

function addRel($mysqli, $idApplication, $idStandard, $relation) {
    $sql = 'INSERT INTO `applicationstandard` (idApplication, idStandard, relation) VALUES ('.$idApplication.', '.$idStandard.', '.$relation.')';
    //echo "Add relation for ".$idStandard."<br/>";
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sql."<br/>";
    $result = $mysqli->query($sql);
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result: ".$result."<br/>";
}
function chgRel($mysqli, $idApplication, $idStandard, $relation) {
    $sql = 'UPDATE `applicationstandard` SET relation = '.$relation.' WHERE idApplication = '.$idApplication.' AND idStandard = '.$idStandard;
    //echo "Change relation for ".$idStandard."<br/>";
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sql."<br/>";
    $result = $mysqli->query($sql);
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result: ".$result."<br/>";
}
function delRel($mysqli, $idApplication, $idStandard, $relation) {
    $sql = 'DELETE FROM `applicationstandard` WHERE idApplication = '.$idApplication.' AND idStandard = '.$idStandard.' AND relation = '.$relation;
    //echo "Delete relation for ".$idStandard."<br/>";
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sql."<br/>";
    $result = $mysqli->query($sql);
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result: ".$result."<br/>";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Application - Components</title>
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
		            <h2>Project <?php echo $project_name;?> - Application <?php echo $application_name;?> - Relations</h2>
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

$arr_rel = array();

$sql = "SELECT * FROM `standard` AS s, `applicationstandard` AS aps WHERE s.id = aps.idStandard AND aps.idApplication = ".$idApplication." ORDER BY `idStandard` ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {

    // output data of each row
    while($row = $result->fetch_assoc()) {
        $arr_rel[$row["id"]] = $row["name"]."|".$row["relation"];
    }
}


$sql = "SELECT * FROM `standard` ORDER BY `id`";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

echo "<form>";
echo "<input type='hidden' name='idProject' value='".$idProject."' />";
echo "<input type='hidden' name='idApplication' value='".$idApplication."' />";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<input type='hidden' name='idStandard' value='".$row["id"]."' />";
        if (array_key_exists($row["id"], $arr_rel)) {
            echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
            //echo "<a href='open_component_editor.php?id=".$row["id"]."&idProject=".$idProject."&idApplication=".$idApplication."'>".$row["id"]." <b>".$arr_rel[$row["id"]]."</b></a>";
            echo $row["id"]." <b>".explode("|", $arr_rel[$row["id"]])[0]."</b>";
            //echo "<span style='float:right;'>[DEL]&nbsp;</span>";
            echo "<button style='float:right;height:20px;display:flex;justify-content:center;align-items:center;' type='submit' class='btn btn-danger remove-item' name='del_relation' onclick='return confirm(\"Are you sure to delete this component?\")'>DEL</button>";
            echo "<span style='float:right;height:20px;display:flex;justify-content:center;align-items:center;'>&nbsp;</span>";
            echo "<button style='float:right;height:20px;display:flex;justify-content:center;align-items:center;' type='submit' class='btn btn-edit remove-item' name='chg_relation')'>CHG</button>";
            echo "<span style='float:right;height:20px;display:flex;justify-content:center;align-items:center;'>&nbsp;</span>";
            echo "<select name='sel_relation' style='float:right;height:20px;display:flex;'>";
            if (explode("|", $arr_rel[$row["id"]])[1] == 0) {
                echo "<option value='0' selected>service user</option>";
                echo "<option value='1'>service provider</option>";
            } else {
                echo "<option value='0'>service user</option>";
                echo "<option value='1' selected>service provider</option>";
            }
            echo "</select>";
            echo "</div>";
        } else {
            echo "<div style='height:30px; padding:5px; width:50%; background-color:lightgray;'>";
            //echo "<a href='open_component_editor.php?id=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
            echo $row["id"]." <b>".$row["name"]."</b>";
            //echo "<span style='float:right;'>[ADD]&nbsp;</span>";
            echo "<button style='float:right;height:20px;display:flex;justify-content:center;align-items:center;' type='submit' class='btn btn-success' name='add_relation'>ADD</button>";
            echo "</div>";
        }
        echo "<br/>";
    }
} else {
    echo "0 results";
}

echo "</form>";
?>

<!--<br/>

<h2>Existing Components for this Application</h2>

<?php

$sql = "SELECT * FROM `component` AS c, `applicationcomponent` AS ac WHERE c.id = ac.idComponent AND ac.idApplication = ".$idApplication." ORDER BY `idComponent` ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_component_editor.php?id=".$row["id"]."&idProject=".$idProject."&idApplication=".$idApplication."'>".$row["id"]." <b>".$row["name"]."</b></a>&nbsp;&nbsp;&nbsp;".substr($row["setting"],0,50);
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}

?>
-->

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
					<a class="a_btn" href="open_application.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

				</div>

	</div>
</body>

</html>