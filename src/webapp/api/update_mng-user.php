<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `user` SET `name` = '".$post['name']."', `email` = '".$post['email']."', `setting` = '".$post['setting']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `user` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>