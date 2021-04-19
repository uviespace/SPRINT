<?php

require 'db_config.php';

$num_rec_per_page = 5;

/*if (isset($_GET["page"])) { $page = $_GET["page"]; } else { $page=1; };*/
if (isset($_GET["idStandard"])) { $idStandard = $_GET["idStandard"]; } else { $idStandard=1; };
if (isset($_GET["idParent"])) { $idParent = $_GET["idParent"]; } else { $idParent=1; };

/*$start_from = ($page-1) * $num_rec_per_page;*/

if ($idStandard==0) {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `type` ORDER BY `id` ASC"; 
} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = 
  "SELECT DISTINCT ".
  "t.* ".
  "FROM ".
  "`type` t ".
  "LEFT JOIN ".
  "`enumeration` e ".
  "ON ".
  "t.id = e.idType ".
  "WHERE ".
  "t.idStandard = ".$idStandard." AND ".
  "e.idType IS NOT NULL ".
  "ORDER BY t.domain, t.name";
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