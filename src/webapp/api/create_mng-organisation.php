<?php

require 'db_config.php';

$post = $_POST;

 $sql = 
      "INSERT INTO ".
      "`organisation` ".
      "(`name`, `shortDesc`, `idCountry`, `desc`) ".
      "VALUES ".
      "('".$post['name']."','".$post['shortDesc']."','".$post['idCountry']."','".$post['desc']."')";

 $result = $mysqli->query($sql);

 $sql = "SELECT * FROM `organsiation` ORDER BY id DESC LIMIT 1"; 

 $result = $mysqli->query($sql);

 $data = $result->fetch_assoc();

 //echo json_encode($data);
 header('Location: ../index.php');

?>