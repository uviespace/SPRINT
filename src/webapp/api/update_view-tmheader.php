<?php


  require 'db_config.php';

  
  if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };


  $id  = $_POST["id"];


  $post = $_POST;


  $sql = "UPDATE `parameter` SET `value` = '".$post['value']."', `desc` = '".$post['desc']."' WHERE `id` = '".$id."'";

  $result = $mysqli->query($sql);
  
  
  $sql = "UPDATE `parametersequence` SET `order` = '".$post['order']."', `role` = '".$post['role']."', `group` = '".$post['group']."', `repetition` = '".$post['repetition']."' WHERE `idParameter` = '".$id."'";

  $result = $mysqli->query($sql);
   


$sql = 
  "SELECT ".
  "    p.id, ".
  "    concat(p.domain, ' / ', p.name) AS parameter, ".
  "    ps.order, ".
  "    ps.role, ".
  "    ps.group, ".
  "    ps.repetition, ".
  "    p.value, ".
  "    p.desc ".
  "FROM ".
  "    `parameter` AS p, ".
  "    `parametersequence` AS ps ".
  "WHERE ".
  "    (p.idStandard = ".$idStandard." OR ".
  "    p.idStandard IS NULL) AND ".
  "    ps.idStandard = ".$idStandard." AND ".
  "    ps.idParameter = p.id AND ".
  "    (p.kind = 1 OR ".
  "    p.kind = 0) AND ".
  "    ps.type = 1";


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>