<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`application` ".
  "(`idProject`, `name`, `desc`, `address`, `setting`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['name']."','".$post['desc']."','".$post['address']."','".$post['setting']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `type` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>