<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`packet` ".
  "(`idStandard`, `idParent`, `kind`, `subtype`, `discriminant`, `name`, `shortDesc`, `desc`, `descParam`, `descDest`, `code`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['idParent']."','".$post['kind']."','".$post['subtype']."','".$post['discriminant']."','".$post['name']."','".$post['shortDesc']."','".$post['desc']."','".$post['descParam']."','".$post['descDest']."','".$post['code']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `packet` ORDER BY `discriminant` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>