<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };

$start_from = ($page-1) * $num_rec_per_page;

$sqlTotal = "SELECT * FROM `organisation`";
$sql = "SELECT * FROM `organisation` ORDER BY `id` ASC LIMIT $start_from, $num_rec_per_page";

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