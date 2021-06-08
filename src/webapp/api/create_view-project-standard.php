<?php

require 'db_config.php';

$post = $_POST;

$setting = "{\"datapool\":{\"parameter\":{\"offset\":1},\"variable\":{\"offset\":9}}}";

$sql = 
  "INSERT INTO ".
  "`standard` ".
  "(`idProject`, `name`, `desc`, `setting`) ".
  "VALUES ".
  "('".$post['idProject']."','".$post['name']."','".$post['desc']."','".$setting."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `standard` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>