<?php


  require 'db_config.php';


  $idStandard  = $_POST["idStandard"];
  $idCalibration  = $_POST["idCalibration"];

  $post = $_POST;

  if ($idCalibration != "0") {
    $sql = 
      "UPDATE `calibration` ".
      "SET ".
      "`name` = '".$post['name']."', ".
      "`shortDesc` = '".$post['shortDesc']."', ".
      "`setting` = '".$post['setting']."' ".
      "WHERE ".
      "`idStandard` = ".$idStandard." AND ".
      "`id` = ".$idCalibration;
  } else {
    $sql =
      "INSERT INTO ".
      "`calibration` ".
      "(`idStandard`, `type`, `name`, `shortDesc`, `setting`) ".
      "VALUES ".
      "(".$idStandard.", ".$post['type'].", '".$post['name']."','".$post['shortDesc']."','".$post['setting']."')";
  }

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `applicationcomponent` WHERE `idApplication` = '".$idApplication."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>