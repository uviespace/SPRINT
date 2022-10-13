<?php

require 'db_config.php';

if (isset($_GET["idType"])) { $idType  = $_GET["idType"]; } else { $idType=0; };

if ($idType==0) {
$sqlTotal = "SELECT `schema` FROM `type`";
$sql = "SELECT `schema FROM `type` ORDER BY `id`";
} else {
$sqlTotal = 
  "SELECT `schema` ".
  "FROM `type` ".
  "WHERE id = ".$idType;
$sql = 
  "SELECT ".
  "    `schema` ".
  "FROM `type` ".
  "WHERE id = ".$idType." ".
  "ORDER BY `id`"; 
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);

echo json_encode($data);

?>