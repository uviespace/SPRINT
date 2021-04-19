<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;
  
  if($post['group']=="null" && $post['repetition']=="null") {
    $sql = "UPDATE ".
           "`parametersequence` ".
           "SET ".
           "`idParameter` = '".$post['parameter']."', ".
           "`order` = '".$post['order']."', ".
           "`role` = '".$post['role']."', ".
           "`value` = '".$post['value']."', ".
           "`desc` = '".$post['desc']."' ".
           "WHERE ".
           "`id` = '".$id."'";
  } else if ($post['group']=="null") {
    $sql = "UPDATE ".
           "`parametersequence` ".
           "SET ".
           "`idParameter` = '".$post['parameter']."', ".
           "`order` = '".$post['order']."', ".
           "`role` = '".$post['role']."', ".
           "`repetition` = '".$post['repetition']."', ".
           "`value` = '".$post['value']."', ".
           "`desc` = '".$post['desc']."' ".
           "WHERE ".
           "`id` = '".$id."'";
  } else if ($post['repetition']=="null") {
    $sql = "UPDATE ".
           "`parametersequence` ".
           "SET ".
           "`idParameter` = '".$post['parameter']."', ".
           "`order` = '".$post['order']."', ".
           "`role` = '".$post['role']."', ".
           "`group` = '".$post['group']."', ".
           "`value` = '".$post['value']."', ".
           "`desc` = '".$post['desc']."' ".
           "WHERE ".
           "`id` = '".$id."'";
  } else {
    $sql = "UPDATE ".
           "`parametersequence` ".
           "SET ".
           "`idParameter` = '".$post['parameter']."', ".
           "`order` = '".$post['order']."', ".
           "`role` = '".$post['role']."', ".
           "`group` = '".$post['group']."', ".
           "`repetition` = '".$post['repetition']."', ".
           "`value` = '".$post['value']."', ".
           "`desc` = '".$post['desc']."' ".
           "WHERE ".
           "`id` = '".$id."'";
  } 

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `parametersequence` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>