<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`projectorganisation` ".
  "(`idProject`, `idOrg`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['idOrg']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `projectorganisation` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>