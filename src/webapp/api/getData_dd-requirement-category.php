<?php

require 'db_config.php';

$num_rec_per_page = 5;

/*if (isset($_GET["page"])) { $page = $_GET["page"]; } else { $page=1; };*/
if (isset($_GET["id"])) { $id = $_GET["id"]; } else { $id=0; };
if (isset($_GET["idProject"])) { $idProject = $_GET["idProject"]; } else { $idProject=0; };

/*$start_from = ($page-1) * $num_rec_per_page;*/

if ($id==0 ) {
if ($idProject==0) {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `projectrequirementcategory` ORDER BY `category` ASC"; 
} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `projectrequirementcategory` WHERE `idProject` = ".$idProject." ORDER BY `category` ASC"; 
}
} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `projectrequirementcategory` WHERE `idProject` = ".$idProject." AND `id` = ".$id." ORDER BY `category` ASC"; 
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

/*$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);*/

echo json_encode($data);

?>