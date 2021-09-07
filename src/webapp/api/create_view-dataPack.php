<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`projectdatapack` ".
  "(`idProject`, `idPackage`, `name`, `note`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['idPackage']."','".$post['name']."','".$post['note']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `projectdatapack` ORDER BY `id` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>