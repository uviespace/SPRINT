<?php

require 'db_config.php';

$num_rec_per_page = 5;

/*if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };*/
if (isset($_GET["filter"])) { $filter  = $_GET["filter"]; } else { $filter=1; };

/*$start_from = ($page-1) * $num_rec_per_page;*/

if (!is_numeric($filter)) {
	$filter_arr = explode(";", $filter);
	$sql = "SELECT * FROM `parameterrole` WHERE `filter` IN (".$filter_arr[0].", ".$filter_arr[1].", 0) ORDER BY id ASC";
} else {
	$sql = "SELECT * FROM `parameterrole` WHERE `filter` IN (".$filter.", 0) ORDER BY id ASC";
}

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