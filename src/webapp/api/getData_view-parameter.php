<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idStandard==0) {
$sqlTotal = "SELECT * FROM `parameter`";
$sql = "SELECT * FROM `parameter` ORDER BY domain, name DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sqlTotal = 
  "SELECT ".
  "    p.id, ".
  "    p.domain, ".
  "    p.name, ".
  "    p.kind, ".
  "    p.shortDesc, ".
  "    p.idType, ".
  "    concat(t.domain, ' / ', t.name) AS datatype, ".
  "    p.multiplicity, ".
  "    p.value, ".
  "    p.unit ".
  "FROM ".
  "    `parameter` AS p, ".
  "    `type` AS t ".
  "WHERE ".
  "    p.idStandard = ".$idStandard." AND ".
  "    p.idType = t.id AND ".
  "    (p.kind = 0 OR ".
  "    p.kind = 1 OR ".
  "    p.kind = 2) ";
$sql = 
  "SELECT ".
  "    p.id, ".
  "    p.domain, ".
  "    p.name, ".
  "    p.kind, ".
  "    p.shortDesc, ".
  "    p.idType, ".
  "    concat(t.domain, ' / ', t.name) AS datatype, ".
  "    p.multiplicity, ".
  "    p.value, ".
  "    p.unit ".
  "FROM ".
  "    `parameter` AS p, ".
  "    `type` AS t ".
  "WHERE ".
  "    p.idStandard = ".$idStandard." AND ".
  "    p.idType = t.id AND ".
  "    (p.kind = 0 OR ".
  "    p.kind = 1 OR ".
  "    p.kind = 2) ".
  "    ORDER BY p.domain, p.name ASC LIMIT $start_from, $num_rec_per_page"; 
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