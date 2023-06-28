<?php

require 'db_config.php';

$post = $_POST;

// check if JSON structure exists
$sql = "SELECT p.setting FROM `parameter`p WHERE p.id = ".$post["idParameter"];

$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

//echo "JSON: ".$row["setting"];

if ($row["setting"] == "") {
    $sql = "UPDATE `parameter` SET `setting` = '{\"calcurve\":".$post["calcurve"]."}' WHERE id = ".$post["idParameter"];
    //echo $sql;
    $result = $mysqli->query($sql);
} else {
    $sql = "UPDATE `parameter` SET `setting` = JSON_MERGE(`setting`, '{\"calcurve\":".$post["calcurve"]."}') WHERE id = ".$post["idParameter"];
    $result = $mysqli->query($sql);
}

/*
$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

echo json_encode($data);*/
/*
$sql = 
  "INSERT INTO ".
  "`limit` ".
  "(`idParameter`,`type`, `lvalue`, `hvalue`, `setting`) ".
  "VALUES ".
  "('".$post['idParameter']."','".$post['type']."','".$post['lvalue']."','".$post['hvalue']."','".$post['setting']."')";

$result = $mysqli->query($sql);*/

$sql = "SELECT * FROM `parameter` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>