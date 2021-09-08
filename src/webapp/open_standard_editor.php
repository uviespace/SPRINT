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

?>

<head>
	<title>Component <?php echo $component_name;?> </title>
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


<h2>Editor for Standard Setting</h2>

<?php
$sql = "SELECT * FROM `standard` WHERE `id` = ".$idStandard." AND `idProject` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    echo "setting: " . $row["setting"]. "<br/><br/>";
} else {
    echo "0 results";
}
?>



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
            echo "<div id='editor_holder'></div>";
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
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>



	</div>
</body>

</html>