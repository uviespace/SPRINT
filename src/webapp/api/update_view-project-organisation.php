<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `projectorganisation` SET `idOrg` = '".$post['idOrg']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `projectorganisation` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>