<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;
  

  $sql = "UPDATE `parameter` ".
    "SET ".
	"`domain` = '".$post['domain']."', ".
	"`name` = '".$post['name']."', ".
	"`shortDesc` = '".$post['shortDesc']."', ".
	"`idType` = '".$post['idType']."', ".
	"`kind` = '".$post['kind']."', ".
	"`multiplicity` = '".$post['multiplicity']."', ".
	"`value` = '".$post['value']."', ".
	"`unit` = '".$post['unit']."' ".
	"WHERE ".
	"`id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `parameter` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>