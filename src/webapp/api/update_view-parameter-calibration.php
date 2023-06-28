<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;
  

  $sql = 
    "UPDATE ".
    "`parameter` p ".
    "SET ".
    "p.setting = JSON_SET(p.setting, '$.calcurve', CAST('".$post["calcurve"]."' AS UNSIGNED)) ".
    "WHERE ".
    "p.id = ".$post["idParameter"]." ";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `calibration` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>