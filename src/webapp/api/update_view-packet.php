<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;


  $sql = "UPDATE `packet` SET `kind` = '".$post['kind']."', `type` = '".$post['type']."', `subtype` = '".$post['subtype']."', `discriminant` = '".$post['discriminant']."', `domain` = '".$post['domain']."', `name` = '".$post['name']."', `shortDesc` = '".$post['shortDesc']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `packet` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>