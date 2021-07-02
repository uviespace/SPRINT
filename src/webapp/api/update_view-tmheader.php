<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;


  $sql = "UPDATE `parametersequence` SET `order` = '".$post['order']."', `role` = '".$post['role']."', `group` = '".$post['group']."', `repetition` = '".$post['repetition']."', `value` = '".$post['value']."', `desc` = '".$post['desc']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `parametersequence` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>