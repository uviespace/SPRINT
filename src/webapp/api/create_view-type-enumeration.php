<?php

require 'db_config.php';

$post = $_POST;

// check if enumeration with same name already exists
$sql = 
  "SELECT t.domain, t.name, e.name AS ename FROM ".
  "`type` AS t, `enumeration` AS e ".
  "WHERE ".
  "t.id = e.idType AND ".
  "t.idStandard = ".$post['idStandard']." AND ".
  //"e.idType = ".$post['idType']." AND ".
  "e.name = '".$post['name']."'";

$result = $mysqli->query($sql);
$row_cnt_enum = $result->num_rows;

if ($row_cnt_enum == 0) {

$sql = 
  "INSERT INTO ".
  "`enumeration` ".
  "(`idType`, `name`, `value`, `desc`) ".
  "VALUES ".
  "('".$post['idType']."','".$post['name']."','".$post['value']."','".$post['desc']."')";

$result = $mysqli->query($sql);

//$sql = "SELECT * FROM `enumeration` ORDER BY `value` DESC LIMIT 1"; 
$sql = "SELECT * FROM `enumeration` ORDER BY `value` DESC"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

 } else {
     
$data = $result->fetch_assoc();
$datatype_domain = $data['domain'];
$datatype_name = $data['name'];
$enumeration_name = $data['ename'];
     
$sql = 
  "SELECT * FROM ".
  "`type` AS t, `enumeration` AS e ".
  "WHERE ".
  "t.id = e.idType AND ".
  "t.idStandard = ".$post['idStandard']." AND ".
  "e.idType = ".$post['idType']." AND ".
  "e.name = '".$post['name']."'";

$result = $mysqli->query($sql);
$row_cnt_enum = $result->num_rows;
     
if ($row_cnt_enum == 0) {
     
     $response["status"] = 3001; //Make up your own error codes! Yippee! Fun!
     $response["statusText"] = "Error: There exists already an enumeration '".$enumeration_name."' for datatype '".$datatype_domain."/".$datatype_name."' in this standard!";
     echo json_encode($response);
     
} else {
    
     $response["status"] = 3002; //Make up your own error codes! Yippee! Fun!
     $response["statusText"] = "Error: There exists already an enumeration '".$enumeration_name."' for this datatype!";
     echo json_encode($response);
    
}
     
 }

?>