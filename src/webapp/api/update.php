<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;


  $sql = "UPDATE `limit` SET idParameter = '".$post['idParameter']."', type = '".$post['type']."', lvalue = '".$post['lvalue']."', hvalue = '".$post['hvalue']."', setting = '".$post['setting']."' WHERE id = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `limit` WHERE id = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>