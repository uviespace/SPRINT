<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $sql = 
  "UPDATE `projectdatapack` ".
  "SET ".
  "`idPackage` = '".$post['idPackage']."', ".
  "`name` = '".$post['name']."', ".
  "`note` = '".$post['note']."' ".
  "WHERE ".
  "`id` = '".$id."'";

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `projectdatapack` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>