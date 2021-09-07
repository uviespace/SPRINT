<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `organisation` SET `name` = '".$post['name']."', `shortDesc` = '".$post['shortDesc']."', `idCountry` = '".$post['idCountry']."', `desc` = '".$post['desc']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `organisation` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>