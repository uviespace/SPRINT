<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idProject==0) {
$sqlTotal = "SELECT * FROM `projectorganisation`";
$sql = "SELECT * FROM `projectorganisation` ORDER BY `id` ASC LIMIT $start_from, $num_rec_per_page";
} else {
$sqlTotal = "SELECT ".
  "po.id AS id, o.id AS idOrg, o.name, o.shortDesc, o.idCountry, o.desc ".
  "FROM ".
  "`projectorganisation` AS po, `organisation` AS o ".
  "WHERE ".
  "po.idProject = o.id AND ".
  "po.idProject = ".$idProject." ";
$sql = "SELECT ".
  "po.id AS id, o.id AS idOrg, o.name, o.shortDesc, o.idCountry, o.desc ".
  "FROM ".
  "`projectorganisation` AS po, `organisation` AS o ".
  "WHERE ".
  "po.idOrg = o.id AND ".
  "po.idProject = ".$idProject." ".
  "ORDER BY po.id ASC LIMIT $start_from, $num_rec_per_page";
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