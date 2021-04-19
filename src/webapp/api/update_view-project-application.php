<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $sql = 
  "UPDATE `application` ".
  "SET ".
  "`name` = '".$post['name']."', ".
  "`desc` = '".$post['desc']."', ".
  "`address` = '".$post['address']."', ".
  "`setting` = '".$post['setting']."' ".
  "WHERE ".
  "`id` = '".$id."'";

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `application` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>