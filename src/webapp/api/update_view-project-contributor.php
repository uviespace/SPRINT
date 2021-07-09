<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `userproject` SET `email` = '".$post['email']."', `idRole` = '".$post['idRole']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `user` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>