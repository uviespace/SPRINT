<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;


  $sql = "UPDATE `enumeration` SET `name` = '".$post['name']."', `value` = '".$post['value']."', `desc` = '".$post['desc']."' WHERE `id` = '".$id."'";

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `enumeration` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>