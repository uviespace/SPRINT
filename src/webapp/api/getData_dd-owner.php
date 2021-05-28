<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };

$start_from = ($page-1) * $num_rec_per_page;

/*$sqlTotal = "SELECT * FROM `parameterrole`";*/
/*$sql = "SELECT DISTINCT u.id, u.name, u.email FROM `user` AS u, `userproject` AS up WHERE u.id = up.idUser AND up.idRole = 2 ORDER BY id ASC";*/
$sql = "SELECT DISTINCT id, name, email FROM `user` ORDER BY name ASC";

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

/*$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);*/

echo json_encode($data);

?>