<?php


  require 'db_config.php';

  if (isset($_GET["idType"])) { $idType  = $_GET["idType"]; } else { $idType=0; };

  $id  = $_POST["id"];

  $post = $_POST;
  
  //print_r($post);

if ($idType!=0) {
    
  $sql = 
  "UPDATE `enumeration` ".
  "SET ".
  "`desc` = '".$post['desc']."', ".
  "`value` = '".$post['value']."' ".
  "WHERE ".
  "`idType` = ".$idType." AND ".
  "`name` = '".$post['name']."'";

  $result = $mysqli->query($sql);

}

  $sql = "SELECT * FROM `enumeration` WHERE `idType` = ".$idType; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);

?>