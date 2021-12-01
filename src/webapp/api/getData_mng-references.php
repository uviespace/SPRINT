<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };

$start_from = ($page-1) * $num_rec_per_page;

$sqlTotal = "SELECT * FROM `document`";
$sql = "SELECT ".
  "o.name AS oname, d.idDocType, d.name, dv.id, dv.identifier, dv.version, dv.date, dv.filename ".
  "FROM ".
  "`organisation` AS o, `document` AS d, `docversion` AS dv ".
  "WHERE ".
  "dv.idDocument = d.id AND ".
  "d.idOrg = o.id ".
  "ORDER BY str_to_date(dv.date,'%d.%m.%Y') ASC LIMIT $start_from, $num_rec_per_page";

/*$sql = "SELECT project.id, project.name, project.desc, user.name AS owner, project.isPublic, project.setting FROM `user`, `userproject`, `project` WHERE userproject.idProject = project.id AND userproject.idUser = user.id AND userproject.idRole = 2 ORDER BY project.id DESC LIMIT $start_from, $num_rec_per_page";*/

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