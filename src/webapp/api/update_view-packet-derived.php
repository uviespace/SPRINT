<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;


  $sql = "UPDATE `packet` SET `discriminant` = '".$post['discriminant']."', `name` = '".$post['name']."', `shortDesc` = '".$post['shortDesc']."', `desc` = '".$post['desc']."', `descParam` = '".$post['descParam']."', `descDest` = '".$post['descDest']."', `code` = '".$post['code']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `packet` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>