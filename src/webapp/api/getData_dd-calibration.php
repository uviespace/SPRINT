<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=1; };

$start_from = ($page-1) * $num_rec_per_page;

if ($idStandard==0) {
$sql = "SELECT * FROM `calibration` ORDER BY `name` ASC"; 
} else {
$sql = "SELECT * FROM `calibration` WHERE `idStandard` = ".$idStandard." ORDER BY `name` ASC"; 
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