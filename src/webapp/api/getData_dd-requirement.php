<?php

require 'db_config.php';

$num_rec_per_page = 5;

/*if (isset($_GET["page"])) { $page = $_GET["page"]; } else { $page=1; };*/
if (isset($_GET["idReqList"])) { $idReqList = $_GET["idReqList"]; } else { $idReqList=0; };
if (isset($_GET["id"])) { $id = $_GET["id"]; } else { $id=0; };

/*$start_from = ($page-1) * $num_rec_per_page;*/

if ($idReqList == 5) { // 11C
    $idDocVersion = 11;
} else if ($idReqList == 6) { // 40C 
    $idDocVersion = 10;
} else if ($idReqList == 7) { // 80C 
    $idDocVersion = 13;
} else if ($idReqList == 8) { // 41A [PUS-A]
    $idDocVersion = 9;
} else if ($idReqList == 9) { // 41C [PUS-C]
    $idDocVersion = 12;
} else {
    $idDocVersion = 0;
}

if ($id==0) {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `requirement` WHERE `idDocVersion` = ".$idDocVersion." ORDER BY `clause` ASC"; 
} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = "SELECT * FROM `requirement` WHERE `id` = ".$id." ORDER BY `clause` ASC"; 
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