<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $sql = 
  "UPDATE `type` ".
  "SET ".
  "`schema` = '".$post['schema']."' ".
  "WHERE ".
  "`id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `type` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>