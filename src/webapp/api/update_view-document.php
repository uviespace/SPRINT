<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $sql = 
  "UPDATE `document` ".
  "SET ".
  "`shortName` = '".$post['shortName']."', ".
  "`number` = '".$post['number']."', ".
  "`name` = '".$post['name']."' ".
  "`idDocType` = '".$post['idDocType']."', ".
  "`idDocRelation` = '".$post['idDocRelation']."' ".
  "WHERE ".
  "`id` = '".$id."'";

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `process` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>