<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idStandard==0) {
$sqlTotal = "SELECT * FROM `packet`";
$sql = "SELECT * FROM `packet` ORDER BY id DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sqlTotal = 
  "SELECT ".
  "p.* ".
  "FROM ".
  "`packet` AS p, ".
  "`applicationpacket` AS ap  ".
  "WHERE ".
  "ap.idApplication = ".$idApplication." AND ".
  "ap.idStandard = ".$idStandard." AND ".
  "ap.idPacket = p.id ".
  "ORDER BY type, subtype ASC";
$sql = 
  "SELECT ".
  "p.* ".
  "FROM ".
  "`packet` AS p, ".
  "`applicationpacket` AS ap  ".
  "WHERE ".
  "ap.idApplication = ".$idApplication." AND ".
  "ap.idStandard = ".$idStandard." AND ".
  "ap.idPacket = p.id ".
  "ORDER BY type, subtype ASC ".
  "LIMIT $start_from, $num_rec_per_page"; 
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