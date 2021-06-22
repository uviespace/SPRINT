<?php


  require 'db_config.php';


  $idStandard  = $_POST["idStandard"];
  $idProject  = $_POST["idProject"];

  $post = $_POST;

  $sql = 
  "UPDATE `standard` ".
  "SET ".
  "`setting` = '".$post['setting']."' ".
  "WHERE ".
  "`id` = ".$idStandard." AND ".
  "`idProject` = ".$idProject;

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `standard` WHERE `id` = '".$idStandard."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>