<?php
header('Content-type:application/json');

require 'db_config.php';

if (isset($_GET["idType"])) { $idType  = $_GET["idType"]; } else { $idType=0; };

$sql = "SELECT `setting` FROM `type` WHERE `id` = ".$idType; 

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
    //echo $row["setting"];
    if ($row["setting"] != "") {
        $json[] = $row;
    } else {
        $json[] = "{}";
    }
}

//$data['data'] = $json;
$data = $json[0];

   //$data = str_replace ( "\'", "&#039;", $data ); // convert single quote
   //$data = str_replace ( "\"", "&quot;", $data ); // convert double-quote
   $data = str_replace ( "\r\n", "", $data ); // remove \r\n
   $data = str_replace ( "\\", "", $data ); // remove slash
   
echo json_encode($data);

?>