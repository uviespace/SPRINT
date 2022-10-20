<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };

$start_from = ($page-1) * $num_rec_per_page;

$sql = 
  "SELECT ".
  "* ".
  "FROM ".
  "`packet` p ".
  "WHERE ".
  "p.id NOT IN ".
  "(SELECT idPacket FROM `applicationpacket` ap WHERE ap.idApplication = ".$idApplication.") AND ".
  "p.idStandard = ".$idStandard." AND ".
  "p.type IS NOT NULL ".
  "ORDER BY p.type, p.subtype ASC";

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

echo json_encode($data);

?>