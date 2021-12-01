<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["userid"])) { $userid  = $_GET["userid"]; } else { $userid=0; };
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };

$start_from = ($page-1) * $num_rec_per_page;

$sqlTotal = "SELECT * FROM `project`";
/*$sql = "SELECT * FROM `project` ORDER BY `id` DESC LIMIT $start_from, $num_rec_per_page"; */

if ($userid == 1 OR $userid == 1001) {
$sql = "SELECT project.id, project.name, project.desc, user.name AS owner, project.isPublic, project.setting FROM `user`, `userproject`, `project` WHERE userproject.idProject = project.id AND userproject.idUser = user.id AND userproject.idRole = 2 ORDER BY project.id DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sql = "SELECT project.id, project.name, project.desc, user.name AS owner, project.isPublic, project.setting FROM `user`, `userproject`, `project` WHERE userproject.idProject = project.id AND userproject.idUser = user.id AND userproject.idRole = 2 AND userproject.idUser = ".$userid." ORDER BY project.id DESC LIMIT $start_from, $num_rec_per_page"; 
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