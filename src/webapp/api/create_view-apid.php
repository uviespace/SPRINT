<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`process` ".
  "(`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `value`, `size`, `unit`, `multiplicity`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['idType']."','".$post['kind']."','".$post['domain']."','".$post['name']."','".$post['shortDesc']."','".$post['value']."','".$post['size']."','".$post['unit']."','".$post['multiplicity']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `parameter` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>