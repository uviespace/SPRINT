<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`service` ".
  "(`idStandard`, `name`, `desc`, `type`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['name']."','".$post['desc']."','".$post['type']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `service` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>