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
	<title>Projects</title>
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
		            <h2>Projects</h2>
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

//require 'api/db_config.php';

if ($userid == 1 || $userid == 1001) {
    $sql = "SELECT * FROM `project` ORDER BY `id`";
} else {
    $sql = "SELECT p.* ".
           "FROM `project` p ".
           "LEFT JOIN `userproject` up ".
           "ON p.id = up.idProject ".
           "WHERE p.isPublic = 1 ".
           "OR (up.idUser = ".$userid." ".
           "AND up.idRole = 2) ".
           "OR (up.email = '".$userEmail."' ".
           "AND up.idRole = 3) ".
           "ORDER BY p.id";
}

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_project.php?id=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        if ($row["isPublic"] == 1) {
            echo "<span style='float:right;'><img src='img/isPublic20.png' />&nbsp;</span>";
        }
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}


?>

<!--
				<div>
					<br/>
					<a href="tab_project.php" target="_self">Table project</a><br/>
					<br/>
				    <a href="tab_application.php" target="_self">Table application</a><br/>
					<a href="tab_standard.php" target="_self">Table standard</a><br/>
					<br/>
					<a href="tab_service.php" target="_self">Table service</a><br/>
					<a href="tab_packet.php" target="_self">Table packet</a><br/>
					<a href="tab_parameter.php" target="_self">Table parameter</a><br/>
					<a href="tab_type.php" target="_self">Table type</a><br/>
					<a href="tab_constants.php" target="_self">Table constants</a><br/>
					<br/>
					<a href="tab_enumeration.php" target="_self">Table enumeration</a><br/>
					<a href="tab_limit.php" target="_self">Table limit</a><br/><br/>
					Data Pool (from Table parameter)
					<br/>
					<a href="tab_packet_test.php" target="_self">Table packet NEW</a><br/>
					<br/>
					<br/>
					<a href="mng_project.php" target="_self">Manage my projects...</a><br/>
					<a href="open_project.php" target="_self">Open project</a><br/>
				</div>
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
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

				</div>

	</div>
</body>

</html>