<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idDataPack"])) { $idDataPack  = $_GET["idDataPack"]; } else { $idDataPack=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idDataPack==0) {
$sqlTotal = "SELECT * FROM `docdatapack`";
$sql = "SELECT * FROM `docdatapack` ORDER BY id DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sqlTotal = "SELECT ".
  "dv.id AS idReference, ddp.*, dv.*, d.* ".
  "FROM ".
  "`docdatapack` AS ddp, `docVersion` AS dv, `document` AS d ".
  "WHERE ".
  "ddp.idDataPack = ".$idDataPack." AND ".
  "ddp.idDocVersion = dv.id AND ".
  "dv.idDocument = d.id";
$sql = "SELECT ".
  "dv.id AS idReference, ddp.*, dv.*, d.* ".
  "FROM ".
  "`docdatapack` AS ddp, `docVersion` AS dv, `document` AS d ".
  "WHERE ".
  "ddp.idDataPack = ".$idDataPack." AND ".
  "ddp.idDocVersion = dv.id AND ".
  "dv.idDocument = d.id ".
  "ORDER BY dv.version ASC LIMIT $start_from, $num_rec_per_page"; 
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