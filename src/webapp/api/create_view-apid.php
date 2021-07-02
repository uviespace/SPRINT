<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`process` ".
  "(`idProject`, `name`, `desc`, `address`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['name']."','".$post['desc']."','".$post['address']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `process` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>