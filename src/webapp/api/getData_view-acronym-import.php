<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["dpDomain"])) { $dpDomain  = $_GET["dpDomain"]; } else { $dpDomain=""; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idProject==0) {
$sqlTotal = "SELECT * FROM `acronym`";
$sql = "SELECT * FROM `acronym` ORDER BY id DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sqlTotal = 
  "SELECT ".
  "    pa.*, a.*, pa.id AS id ".
  "FROM ".
  "    `projectacronym` AS pa, ".
  "    `acronym` AS a ".
  "WHERE ".
  "    pa.idProject = ".$idProject." AND ".
  "    pa.idAcronym = a.id ";
$sql = 
  "SELECT ".
  "    pa.*, a.*, pa.id AS id ".
  "FROM ".
  "    `projectacronym` AS pa, ".
  "    `acronym` AS a ".
  "WHERE ".
  "    pa.idProject = ".$idProject." AND ".
  "    pa.idAcronym = a.id ".
  "    ORDER BY a.name ASC LIMIT $start_from, $num_rec_per_page"; 
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