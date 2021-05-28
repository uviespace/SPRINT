<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`user` ".
  "(`name`, `email`, `setting`) ".
  "VALUES ".
  "('".$post['name']."','".$post['email']."','".$post['setting']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `user` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>