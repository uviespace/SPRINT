<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $pusdt = explode("_", $post['pusdatatype']);

  $sql = 
  "UPDATE `type` ".
  "SET ".
  "`domain` = '".$post['domain']."', ".
  "`name` = '".$post['name']."', ".
  "`nativeType` = '".$post['nativeType']."', ".
  "`size` = '".$post['size']."', ".
  "`value` = '".$post['value']."', ".
  "`desc` = '".$post['desc']."', ".
  "`setting` = JSON_REPLACE(`setting`, '$.PUS.type', '".$post['pusdatatype']."', '$.PUS.ptc', ".$pusdt[1].", '$.PUS.pfc', ".$pusdt[2].") ".
  //"`schema` = '".$post['schema']."' ".
  "WHERE ".
  "`id` = '".$id."'";

// UPDATE `type` SET `setting` = JSON_REPLACE(`setting`, '$.PUS.type', 100) WHERE `id`= 1479 
// {"PUS": {"type": 100, "ptc": 3, "pfc": 4}}

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `type` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>