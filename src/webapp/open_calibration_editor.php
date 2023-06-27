<!DOCTYPE html>
<html>

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

if (isset($_GET["id"])) { $idCalibration  = $_GET["id"]; } else { $idCalibration=0; };
//if (isset($_GET["type"])) { $calibration_type  = $_GET["type"]; } else { $calibration_type="0"; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };


$sql = "SELECT * FROM `calibration` WHERE `id` = ".$idCalibration;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $calibration_type = $row["type"];
        $calibration_name = $row["name"];
        $calibration_shortDesc = $row["shortDesc"];
        $calibration_setting = $row["setting"];
    }
} else {
    $calibration_type = "0";
    $calibration_name = "";
    $calibration_shortDesc = "";
    //echo "0 results";
}
//echo $calibration_type."<br/>";

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

/*
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
*/

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

//echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
//echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";

//echo $calibration_setting."<br/>";
//$keys = array_keys(json_decode($calibration_setting, true));
$data = json_decode($calibration_setting);

if ($calibration_type == "0") {

    //echo $data->engfmt."<br/>";
    $values = $data->values;
    //print_r($values);
    $keys = array_keys($values);
    //print_r($keys);
    
    //echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
    
    foreach ($values as $val) {
        $v = get_object_vars($val);
        
        $v["label"] = $v["xval"];
        unset($v["xval"]);
        $v["y"] = $v["yval"];
        unset($v["yval"]);
        
        //print_r($v);
        $dataPoints[] = $v;
        //echo "<br/>(".$v["label"]."/".$v["y"].")<br/>";
    }
    
    //echo "<br/><br/>";
    
    //print_r($dataPoints);

} else {

    $dataPoints = array(
        array("y" => 25, "label" => "Sunday"),
        array("y" => 15, "label" => "Monday"),
        array("y" => 25, "label" => "Tuesday"),
        array("y" => 5, "label" => "Wednesday"),
        array("y" => 10, "label" => "Thursday"),
        array("y" => 0, "label" => "Friday"),
        array("y" => 20, "label" => "Saturday")
    );
    
    //echo "<br/><br/>"; 
    
    //print_r($dataPoints);

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

<head>
	<title>Calibration <?php echo $calibration_name;?> </title>
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
    <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css'>

    <!--<link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css'>-->
    <!--<link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/themes/modern.min.css'>-->
    <script src='//cdn.jsdelivr.net/jquery/2.1.1/jquery.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/plugins/xhtml.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/json-editor/0.7.28/jsoneditor.min.js"></script>
    <script src='int/json_editor_calibration.js'></script>
    
    
    
    
	<link rel="stylesheet" type="text/css" href="int/layout.css">
    <script type="text/javascript" src="int/config.js"></script>
	<script type="text/javascript">
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	title: {
		text: "Calibration Curve"
	},
	axisY: {
		title: "Y Value"  // Temperature in °C
	},
	data: [{
		type: "line",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}

		function buildProject(idProject, idApplication) {
			toastr.success('Debug: Project: '+idProject+', Application: '+idApplication, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: START ...', 'Success Alert', {timeOut: 5000}); 



			toastr.success('Debug: Output: '+$file, 'Success Alert', {timeOut: 5000}); 
			toastr.success('Debug: ... END', 'Success Alert', {timeOut: 5000}); 
		}
	</script>
	<!--<script type="text/javascript" src="js/item-ajax.js"></script>-->
	<style type="text/css">
@media (min-width: 40em) {
.table { 
   display: table;
   border-spacing: 0.5em;
}
.table-row {display: table-row; }
.table-cell {display: table-cell; }
}
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


<h2>Editor for Calibration Curve <?php echo $calibration_name; ?></h2>

<div>
<!--<label for='type'>Type: </label>
<input id='type' type='text' value='<?php echo $calibration_type; ?>' readonly />
<label for='name'>Name: </label>
<input id='name' type='text' value='<?php echo $calibration_name; ?>'/>
<label for='name'>Short Description: </label>
<input id='shortDesc' type='text' value='<?php echo $calibration_shortDesc; ?>'/>-->

    <input id="type" type="hidden" name="type" value="<?php echo $calibration_type; ?>" />
    <!--<div class="form-group">
        <label class="control-label" for="title">Type:</label>
        <input id="type" type="text" name="type" class="form-control" value="<?php echo $calibration_type; ?>" data-error="Please enter calibration curve type." readonly />
    </div>-->
    <div class="form-group">
        <label class="control-label" for="title">Name:</label>
        <input id="name" type="text" name="name" class="form-control" value="<?php echo $calibration_name; ?>" data-error="Please enter calibration curve name." />
    </div>
    <div class="form-group">
        <label class="control-label" for="title">Short Description:</label>
        <input id="shortDesc" type="text" name="shortDesc" class="form-control" value="<?php echo $calibration_shortDesc; ?>" data-error="Please enter calibration curve short description." />
    </div>

</div>

<?php
$sql = "SELECT * FROM `calibration` WHERE `id` = ".$idCalibration;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    //echo "setting: " . $row["setting"]. "<br/><br/>";
} else {
    echo "0 results";
}
?>


<!--<div id="chartContainer" style="height: 370px; width: 100%;"></div>-->
<!--<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>-->
<!--<script src="ext/canvasjs-chart-3.7.9/canvasjs.min.js"></script>-->

    <hr/>
    
    <div class="chart-container">
      <canvas id="mycanvas"></canvas>
    </div>
        
    <!-- javascript -->
    <!--<script type="text/javascript" src="js/jquery.min.js"></script>-->
    <!--<script type="module" src="ext/chart.js-4.3.0/chart.min.js"></script>-->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.js"></script>-->  <!-- UMD bundle: https://stackoverflow.com/questions/74853338/migration-chart-js-with-zoom-plugin-and-moment-adapter -->
    <script type="text/javascript" src="ext/chart.js-4.3.0/chart.umd.js"></script>
    <?php if ($calibration_type == "0") { ?>
    <script type="text/javascript" src="ext/chart.js-4.3.0/linegraph.js"></script>
    <?php } else if ($calibration_type == "1") { ?>
    <script type="text/javascript" src="ext/chart.js-4.3.0/linegraphPol.js"></script>
    <?php } else if ($calibration_type == "2") { ?>
    <script type="text/javascript" src="ext/chart.js-4.3.0/linegraphLog.js"></script>
    <?php } ?>
    
    <hr/>

<br/><br/>

    <div class='row' style='padding-bottom: 15px;'>
        <div class='col-md-12'>
            <button id='save' class='btn btn-success'>Save</button>
            <button id='submit' class='btn btn-info'>Submit (console.log)</button>
            <button id='restore' class='btn btn-info'>Restore to Default</button>
            <button id='enable_disable' class='btn btn-info'>Disable/Enable Form</button>
            <span id='valid_indicator' class='label label-success'></span>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
<?php
    if ($calibration_type == "0") {
        if ($idCalibration > 0) {
            echo "<div id='editor_holder_num'></div>";
        } else {
            echo "<div id='editor_holder_num_new'></div>";
        }
    } else if ($calibration_type == "1") {
        if ($idCalibration > 0) {
            echo "<div id='editor_holder_pol'></div>";
        } else {
            echo "<div id='editor_holder_pol_new'></div>";
        }
    } else if ($calibration_type == "2") {
        if ($idCalibration > 0) {
            echo "<div id='editor_holder_log'></div>";
        } else {
            echo "<div id='editor_holder_log_new'></div>";
        } 
    }
?>
        </div>
    </div>



				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="sel_parameter-calibration.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>



	</div>
</body>

</html>