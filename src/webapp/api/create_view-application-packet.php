<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`applicationpacket` ".
  "(`idApplication`, `idStandard`, `idPacket`) ".
  "VALUES ".
  "('".$post['idApplication']."','".$post['idStandard']."','".$post['id']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `applicationpacket` ORDER BY `idApplication` DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>