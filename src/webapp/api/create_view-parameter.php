<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`parameter` ".
  "(`idStandard`, `domain`, `name`, `shortDesc`, `idType`, `kind`, `role`, `multiplicity`, `value`, `unit`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['domain']."','".$post['name']."','".$post['shortDesc']."','".$post['idType']."','".$post['kind']."','".$post['role']."','".$post['multiplicity']."','".$post['value']."','".$post['unit']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `parameter` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>
