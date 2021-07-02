<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`packet` ".
  "(`idStandard`, `idProcess`, `kind`, `type`, `subtype`, `domain`, `name`, `shortDesc`, `desc`, `descParam`, `descDest`, `code`, `setting`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['idProcess']."','".$post['kind']."','".$post['type']."','".$post['subtype']."','".$post['domain']."','".$post['name']."','".$post['shortDesc']."','".$post['desc']."','".$post['descParam']."','".$post['descDest']."','".$post['code']."','".$post['setting']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `packet` ORDER BY `type`, `subtype` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>