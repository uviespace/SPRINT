<?php


  require 'db_config.php';


  $idApplication  = $_POST["idApplication"];
  $idComponent  = $_POST["idComponent"];

  $post = $_POST;

  $sql = 
  "UPDATE `applicationcomponent` ".
  "SET ".
  "`setting` = '".$post['setting']."' ".
  "WHERE ".
  "`idApplication` = ".$idApplication." AND ".
  "`idComponent` = ".$idComponent;

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `applicationcomponent` WHERE `idApplication` = '".$idApplication."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>