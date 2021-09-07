<?php

require 'db_config.php';

$post = $_POST;

 $sql = 
      "INSERT INTO ".
      "`acronym` ".
      "(`name`, `shortDesc`, `desc`) ".
      "VALUES ".
      "('".$post['name']."','".$post['shortDesc']."','".$post['desc']."')";

 $result = $mysqli->query($sql);

 $sql = "SELECT * FROM `acronym` ORDER BY id DESC LIMIT 1"; 

 $result = $mysqli->query($sql);

 $data = $result->fetch_assoc();

 //echo json_encode($data);
 header('Location: ../index.php');

?>