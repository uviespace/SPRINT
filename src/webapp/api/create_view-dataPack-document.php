<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`docdatapack` ".
  "(`idDataPack`, `idDocVersion`, `note`) ".
  "VALUES ".
  "('".$post['idDataPack']."','".$post['idReference']."','".$post['note']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `docdatapack` ORDER BY `id` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>