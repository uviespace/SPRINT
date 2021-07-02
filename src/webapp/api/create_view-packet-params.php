<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`parametersequence` ".
  "(`idStandard`, `idParameter`, `idPacket`, `type`, `role`, `order`, `group`, `repetition`, `value`, `desc`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['parameter']."','".$post['idPacket']."','0','".$post['role']."','".$post['order']."','".$post['group']."','".$post['repetition']."','".$post['value']."','".$post['desc']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `parametersequence` ORDER BY `order` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>