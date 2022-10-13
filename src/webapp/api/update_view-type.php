<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $pusdt = explode("_", $post['pusdatatype']);

  $sqlSetting = "SELECT `setting` FROM `type` WHERE id = '".$id."'";
  $resultSetting = $mysqli->query($sqlSetting);
  $dataSetting = $resultSetting->fetch_assoc();
  $setting = $dataSetting["setting"];
  if ($setting == NULL OR $setting == "" OR $setting == "{}") {

  $setting = "{\"PUS\": {\"type\": \"".$post['pusdatatype']."\", \"ptc\": ".$pusdt[1].", \"pfc\": ".$pusdt[2]."}}";

  $sql = 
  "UPDATE `type` ".
  "SET ".
  "`domain` = '".$post['domain']."', ".
  "`name` = '".$post['name']."', ".
  "`nativeType` = '".$post['nativeType']."', ".
  "`size` = '".$post['size']."', ".
  "`value` = '".$post['value']."', ".
  "`desc` = '".$post['desc']."', ".
  "`setting` = '".$setting."' ".
  "WHERE ".
  "`id` = '".$id."'";

  } else {

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
  "WHERE ".
  "`id` = '".$id."'";

// UPDATE `type` SET `setting` = JSON_REPLACE(`setting`, '$.PUS.type', 100) WHERE `id`= 1479 
// {"PUS": {"type": 100, "ptc": 3, "pfc": 4}}

  }

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `type` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>