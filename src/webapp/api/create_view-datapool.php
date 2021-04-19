<?php

require 'db_config.php';

$post = $_POST;

// get bit size from idType
$sql_size = "SELECT `size` FROM `type` WHERE `id` = ".$post['idType'];
$result = $mysqli->query($sql_size);
$row = mysqli_fetch_assoc($result);
$size = $row["size"];

// set desc to empty string
$desc = '';

$sql = 
  "INSERT INTO ".
  "`parameter` ".
  "(`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['idType']."','".$post['kind']."','".$post['domain']."','".$post['name']."','".$post['shortDesc']."','".$desc."','".$post['value']."','".$size."','".$post['unit']."','".$post['multiplicity']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `parameter` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>