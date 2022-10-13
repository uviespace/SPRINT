<?php

require 'db_config.php';

$post = $_POST;

if ($post['gse']==0) {
  $setting = "{\"PUS\": {\"type\": \"\", \"ptc\": null, \"pfc\": null}}";
} else if ($post['gse']==1) {
  $setting = "{\"CC\": {\"type\": \"\", \"property 1\": \"\", \"property2\": \"\"}}";
} else {
  $setting = "";
}

$schema = 
  "{\n".
  "    \"title\": \"Setting\",\n".
  "    \"type\": \"object\",\n".
  "    \"properties\": {\n".
  "        \"Enumerations\" : {\n".
  "            \"type\": \"array\",\n".
  "            \"format\": \"table\",\n".
  "            \"uniqueItems\": true,\n".
  "            \"items\": {\n".
  "                \"type\": \"object\",\n".
  "                \"properties\": {\n".
  "                    \"Value\": { \"type\": \"string\" },\n".
  "                    \"Name\": { \"type\": \"string\" },\n".
  "                    \"Description\" : { \"type\": \"string\", \"format\": \"textarea\" }\n".
  "                    \"Parameters\" : { \"type\": \"string\" }\n".
  "                }\n".
  "            }\n".
  "        }\n".
  "        \"PUS\": {\n".
  "            \"type\": \"object\",\n".
  "            \"properties\": {\n".
  "                \"type\": { \"type\": \"string\" }\n".
  "                \"ptc\": { \"type\":\"integer\" },\n".
  "                \"pfc\": { \"type\": \"integer\" },\n".
  "            }\n".
  "        }\n".
  "        \"CC\": {\n".
    "            \"type\": \"object\",\n".
  "            \"properties\": {\n".
  "                \"type\": { \"type\": \"string\" }\n".
  "                \"property 1\": { \"type\":\"string\" },\n".
  "                \"property 2\": { \"type\": \"string\" },\n".
  "            }\n".
  "        }\n".
  "    }\n".
  "}";

$sql = 
  "INSERT INTO ".
  "`type` ".
  "(`idStandard`, `domain`, `name`, `nativeType`, `size`, `value`, `desc`, `setting`, `schema`) ".
  "VALUES ".
  "('".$post['idStandard']."','".$post['domain']."','".$post['name']."','".$post['nativeType']."','".$post['size']."','".$post['value']."','".$post['desc']."','".$setting."','".$schema."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `type` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>