<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["idParameter"])) { $idParameter  = $_GET["idParameter"]; } else { $idParameter=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idParameter==0) {
//$sqlTotal = "SELECT * FROM `limit`";
//$sql = "SELECT * FROM `limit` ORDER BY `id` DESC LIMIT $start_from, $num_rec_per_page"; 
$sqlTotal = "SELECT * FROM `calibration`";
$sql = "SELECT * FROM `calibration` ORDER BY `id` DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
//$sqlTotal = "SELECT * FROM `limit` WHERE `idParameter` = ".$idParameter;
//$sql = "SELECT * FROM `limit` WHERE `idParameter` = ".$idParameter." ORDER BY `lvalue` ASC LIMIT $start_from, $num_rec_per_page";
$sqlTotal = "SELECT c.id, p.id as idParameter, c.type, c.name, c.shortDesc, p.setting FROM `calibration` c, `parameter` p WHERE JSON_CONTAINS_PATH(p.setting, 'one', '$.calcurve') AND JSON_EXTRACT(p.setting, '$.calcurve') = c.id AND p.id = ".$idParameter;
$sql = "SELECT c.id, p.id as idParameter, c.type, c.name, c.shortDesc, p.setting FROM `calibration` c, `parameter` p WHERE JSON_CONTAINS_PATH(p.setting, 'one', '$.calcurve') AND JSON_EXTRACT(p.setting, '$.calcurve') = c.id AND p.id = ".$idParameter." ORDER BY c.name ASC LIMIT $start_from, $num_rec_per_page";
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