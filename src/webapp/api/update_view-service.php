<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $sql = 
  "UPDATE `service` ".
  "SET ".
  "`type` = '".$post['type']."', ".
  "`name` = '".$post['name']."', ".
  "`desc` = '".$post['desc']."' ".
  "WHERE ".
  "`id` = '".$id."'";

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `service` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>