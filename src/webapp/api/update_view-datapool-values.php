<?php

require 'db_config.php';

$post = $_POST;

// get id
$id  = $_POST["id"];

$sql = "UPDATE `parameter` ".
"SET ".
"`value` = '".$post['values']."' ".
"WHERE ".
"`id` = '".$id."'";

$result = $mysqli->query($sql);


$sql = "SELECT * FROM `parameter` WHERE `id` = '".$id."'"; 


$result = $mysqli->query($sql);


$data = $result->fetch_assoc();


echo json_encode($data);

?>