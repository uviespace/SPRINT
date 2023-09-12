<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`constants` ".
  "(`idStandard`, `domain`, `name`, `desc`, `value`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['domain']."','".$post['name']."','".$post['desc']."','".$post['value']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `constants` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>