<?php

require 'db_config.php';

$post = $_POST;

// Parameter of Header: 'SELECT * FROM `parameter` WHERE idStandard = 1024 AND kind = 1'
// Parametersequence of TC Header: 'SELECT * FROM `parametersequence` WHERE idStandard = 1024 AND type = 0 AND idPacket IS NULL'
// Parametersequence of TM Header: 'SELECT * FROM `parametersequence` WHERE idStandard = 1024 AND type = 1 AND idPacket IS NULL'

// type = 0 for TC header type = 1 for TM header
// idPacket = NULL

$sql = 
  "UPDATE `parameter` SET `kind` = 1 WHERE `id` = ".$post['parameter'];

$result = $mysqli->query($sql);


$sql = 
  "INSERT INTO ".
  "`parametersequence` ".
  "(`idStandard`, `idParameter`, `type`, `role`, `order`, `group`, `repetition`, `value`, `desc`) VALUES ".
  "('".$post['idStandard']."','".$post['parameter']."','".$post['type']."','".$post['role']."','".$post['order']."','".$post['group']."','".$post['repetition']."','".$post['value']."','".$post['desc']."')";

$result = $mysqli->query($sql);


$sql = "SELECT * FROM `parametersequence` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>