<?php
header('Content-type:application/json');

require 'db_config.php';

if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["idCalibration"])) { $idCalibration  = $_GET["idCalibration"]; } else { $idCalibration=0; };

if ($idCalibration > 0) {
  $sql = "SELECT setting FROM `calibration` WHERE `idStandard` = ".$idStandard." AND `id` = ".$idCalibration; 
  
  $result = $mysqli->query($sql);
  
  $json = array();
  
  while($row = $result->fetch_assoc()){
  	$json[] = $row;
  }
  
  //$data['data'] = $json;
  $data = $json[0];
} else {
  $data['setting'] = "{}";
}

//$data = str_replace ( "\'", "&#039;", $data ); // convert single quote
//$data = str_replace ( "\"", "&quot;", $data ); // convert double-quote
$data = str_replace ( "\r\n", "", $data ); // remove \r\n
$data = str_replace ( "\\", "", $data ); // remove slash
   
echo json_encode($data);

?>