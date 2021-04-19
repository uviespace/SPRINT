<?php

require 'db_config.php';

$post = $_POST;

// get id
$id  = $_POST["id"];

// get bit size from idType
$sql_size = "SELECT `size` FROM `type` WHERE `id` = ".$post['idType'];
$result = $mysqli->query($sql_size);
$row = mysqli_fetch_assoc($result);
$size = $row["size"];

$sql = "UPDATE `parameter` ".
"SET ".
"`idType` = '".$post['idType']."', ".
"`kind` = '".$post['kind']."', ".
"`domain` = '".$post['domain']."', ".
"`name` = '".$post['name']."', ".
"`shortDesc` = '".$post['shortDesc']."', ".
"`value` = '".$post['value']."', ".
"`size` = '".$size."', ".
"`unit` = '".$post['unit']."', ".
"`multiplicity` = '".$post['multiplicity']."' ".
"WHERE ".
"`id` = '".$id."'";

$result = $mysqli->query($sql);


$sql = "SELECT * FROM `parameter` WHERE `id` = '".$id."'"; 


$result = $mysqli->query($sql);


$data = $result->fetch_assoc();


echo json_encode($data);

?>