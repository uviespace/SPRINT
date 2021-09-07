<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`projectacronym` ".
  "(`idProject`, `idAcronym`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['idAcronym']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `projectacronym` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>