<?php

require 'db_config.php';

$post = $_POST;

$sql = 
  "INSERT INTO ".
  "`project` ".
  "(`name`, `desc`, `isPublic`, `setting`) ".
  "VALUES ".
  "('".$post['name']."','".$post['desc']."','".$post['isPublic']."','".$post['setting']."')";

$result = $mysqli->query($sql);

// get project id

$projectId = $mysqli->insert_id;

// link project with user as owner

$sql = 
  "INSERT INTO ".
  "`userproject` ".
  "(`idUser`, `idProject`, `idRole`) ".
  "VALUES ".
  "('".$post['userid']."','".$projectId."','2')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `project` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>