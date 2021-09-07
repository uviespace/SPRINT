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

if (isset($_GET["id"])) { $id  = $_GET["id"]; } else { $id=0; };
if (isset($_GET["action"])) { $action  = $_GET["action"]; } else { $action=""; };
$project_name = "Noname";
$idProject = $id;

$sql = "SELECT * FROM `project` WHERE `id`=".$id;

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

if ($action == "exp_acr") {
    //echo "Export List of Acronyms ...<br/>";
    
    $path_tmp = "documentation/out/"; // could also be tmp/
    $filename = "acr_".$project_name.".csv";
    $file = $path_tmp . $filename;
    
    // get file content
    $result = getAcronyms($mysqli, $idProject);
    
    $newcontent = "";
    $delimiter = ";";
    $shortList = false;
    while ($row = $result->fetch_assoc()) {
        if ($shortList) {
            $newcontent .= $row['name'].$delimiter.$row['shortDesc']."\n"; 
        } else {
            $newcontent .= $row['name'].$delimiter.$row['shortDesc'].$delimiter.$row['desc']."\n"; 
        }
    }
    
    // open, write and close file
    $myfile = fopen($file, "w");
    fwrite($myfile, $newcontent);
    fclose($myfile);

    // open/download file in browser
    
    // Header content type
    header('Content-type: 	text/csv');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
    
    // delete file
    unlink($file);
    
    // end page
    die('');

} else if ($action == "exp_ref") {
    //echo "Export List of References ...<br/>";
    
    $path_tmp = "documentation/out/"; // could also be tmp/
    $filename = "ref_".$project_name.".csv";
    $file = $path_tmp . $filename;
    
    // get file content
    $result = getReferences($mysqli, $idProject);
    
    $newcontent = "";
    $delimiter = ";";
    $shortList = false;
    while ($row = $result->fetch_assoc()) {
        if ($shortList) {
            $newcontent .= $row['name'].$delimiter.$row['shortName']."\n"; 
        } else {
            $newcontent .= $row['name'].$delimiter.$row['shortName'].$delimiter.$row['number'].$delimiter.$row['identifier'].$delimiter.$row['version'].$delimiter.$row['date']."\n"; 
        }
    }
    
    // open, write and close file
    $myfile = fopen($file, "w");
    fwrite($myfile, $newcontent);
    fclose($myfile);

    // open/download file in browser
    
    // Header content type
    header('Content-type: 	text/csv');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
    
    // delete file
    unlink($file);
    
    // end page
    die('');
}

function getAcronyms($mysqli, $idProject) {
    $sql = "SELECT ".
      "pa.id AS id, a.id AS idAcronym, a.name, a.shortDesc, a.desc ".
      "FROM ".
      "`projectacronym` AS pa, `acronym` AS a ".
      "WHERE ".
      "pa.idAcronym = a.id AND ".
      "pa.idProject = ".$idProject." ".
      "ORDER BY ".
      "a.name ".
      "ASC";
    
    return $mysqli->query($sql);
}

function getReferences($mysqli, $idProject) {
    /*$sql = "SELECT ".
      "pd.id AS id, d.id AS idDocument, d.name, d.shortName, d.number ".
      "FROM ".
      "`projectdocument` AS pd, `document` AS d ".
      "WHERE ".
      "pd.idDocument = d.id AND ".
      "pd.idProject = ".$idProject." ".
      "ORDER BY ".
      "d.name ".
      "ASC";*/
    
    $sql = "SELECT ".
      "pd.id AS id, d.id AS idDocument, d.name, d.shortName, d.number, dv.* ".
      "FROM ".
      "`projectdocument` AS pd, `document` AS d, `docversion` AS dv ".
      "WHERE ".
      "dv.idDocument = d.id AND ".
      "pd.idDocument = d.id AND ".
      "pd.idProject = ".$idProject." ".
      "ORDER BY ".
      "d.name ".
      "ASC";
    
    return $mysqli->query($sql);
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
	<title>Project <?php echo $project_name;?></title>
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

        .badge {
            position: relative;
            top: -11px;
            left: -10px;
            border: 0px solid black;
            border-radius: 75%;
            background-color: green;
            font-size: 7px;
        }

	</style>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Project <?php echo $project_name;?></h2>
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

$sql = "SELECT * FROM `application` WHERE idProject = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Applications</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_application.php?idProject=$id&idApplication=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    //echo "0 results";
}

?>

<a href="view_project-application.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Applications ...</button></a>

<br/>

<?php

$sql = "SELECT * FROM `standard` WHERE idProject = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Standards</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_standard.php?idProject=$id&idStandard=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    //echo "0 results";
}

?>

<a href="view_project-standard.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Standards ...</button></a>

<br/><br/>

<h3>Document Management</h3>

<a href="sel_project-documentation.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Documents ...</button></a>

<br/><br/>

<a href="view_project-acronyms.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Acronyms ...</button></a>
<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_acr"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php echo mysqli_num_rows(getAcronyms($mysqli, $idProject)); ?></span></span></a>
&nbsp;&nbsp;&nbsp;

<a href="view_project-references.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage References ...</button></a>
<a href="open_project.php?id=<?php echo $idProject; ?>&action=exp_ref"><span id="group"><img src="img/download.png" width="25px" /><span class="badge badge-light"><?php echo mysqli_num_rows(getReferences($mysqli, $idProject)); ?></span></span></a>
&nbsp;&nbsp;&nbsp;

<a href="view_project-organisations.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Organisations ...</button></a>

<br/><br/>

<?php

$sql = "SELECT u.id, u.name, up.email, up.idRole FROM `user` AS u, `project` AS p, `userproject` AS up WHERE u.id = up.idUser AND p.id = up.idProject AND (up.idRole = 3 OR up.idRole = 4) AND p.id = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Contributors / Users</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:#E8E8E8;'>";
        //echo "<a href='open_user.php?idProject=$id&idApplication=".$row["id"]."' >".$row["id"]." <b>".$row["email"]."</b></a>";
        echo "<font color='Gray'>".$row["id"]." <b>".$row["email"]."</b></font>";
        if ($row["idRole"] == 4) {
            echo "<span style='float:right;'><img src='img/guest.png' />&nbsp;</span>";
        }
        echo "</div>";
    }
    echo "<br/>";
} else {
    //echo "0 results";
}

?>

<a href="view_project-contributor.php?idProject=<?php echo $idProject; ?>"><button style="width:180px;">Manage Contributors ...</button></a>

<br/><br/>

<?php

$sql = "SELECT u.id, u.name, u.email FROM `user` AS u, `project` AS p, `userproject` AS up WHERE u.id = up.idUser AND p.id = up.idProject AND up.idRole = 2 AND p.id = $id";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "<h3>Owner</h3>";
//echo $num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:#E8E8E8;'>";
        //echo "<a href='open_user.php?idProject=$id&idApplication=".$row["id"]."' ><font color='Gray'>".$row["id"]." <b>".$row["name"]."</b> (".$row["email"].")</font></a>";
        echo "<font color='Gray'>".$row["id"]." <b>".$row["name"]."</b> (".$row["email"].")</font>";
        echo "</div>";
        echo "<br/>";
    }
} else {
    //echo "0 results";
}

?>

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
					<a class="a_btn" href="sel_project.php" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>