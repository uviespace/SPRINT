<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = 
    "UPDATE `projectrequirement` SET ".
    "`shortDesc` = '".$post['shortDesc']."', ".
    "`desc` = '".$post['desc']."', ".
    "`notes` = '".$post['notes']."', ".
    "`justification` = '".$post['justification']."', ".
    "`applicability` = '".$post['applicability']."', ".
    "`applicableToPayloads` = '".$post['applicableToPL']."' ".
    "WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `projectrequirement` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>