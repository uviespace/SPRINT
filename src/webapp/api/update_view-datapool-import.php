<?php


  require 'db_config.php';

  $id  = $_POST["id"];

  $post = $_POST;
  
  //print_r($post);


  $sql = "UPDATE `parameter` ".
    "SET ".
    "`shortDesc` = '".$post['shortDesc']."', ".
    "`value` = '".$post['value']."' ".
    "WHERE ".
    "`domain` = '".$post['domain']."' AND ".
    "`name` = '".$post['name']."'"; 

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `parameter` WHERE `domain` = '".$post['domain']."' AND `name` = '".$post['name']."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);

?>