<?php


  require 'db_config.php';


  $idStandard  = $_POST["idStandard"];
  $idType  = $_POST["idType"];

  $post = $_POST;

  $sql = 
  "UPDATE `type` ".
  "SET ".
  "`setting` = '".$post['setting']."' ".
  "WHERE ".
  "`idStandard` = ".$idStandard." AND ".
  "`id` = ".$idType;

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `type` WHERE `idStandard` = '".$idStandard."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>