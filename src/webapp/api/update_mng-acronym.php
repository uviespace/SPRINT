<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `acronym` SET `name` = '".$post['name']."', `shortDesc` = '".$post['shortDesc']."', `desc` = '".$post['desc']."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);

  if ($post['idClassification']!='undefined') {
      $sql = "UPDATE `acronymclassification` SET `idClassification` = ".$post['idClassification']." WHERE `idAcronym` = ".$id;
      $result = $mysqli->query($sql);
  }


  $sql = "SELECT * FROM `acronym` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>