<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

$old_version = true;

if ($idStandard==0) {
$sqlTotal = "SELECT * FROM `type`";
$sql = "SELECT * FROM `type` ORDER BY domain, name DESC LIMIT $start_from, $num_rec_per_page"; 
} else {
$sqlTotal = 
  "SELECT * ".
  "FROM `type` ".
  "WHERE idStandard = ".$idStandard;
if ($old_version) {
// for MariaDB older than the version 10.2.3
//  SELECT regexp_substr(setting, '[^[{"PUS": {"type":"]]*[^"]*') FROM type WHERE id = 1500; -----> 14_3_12
//   "    regexp_substr(setting, '[^[{\"PUS\": {\"type\":\"]]*[^\"]*') as `pusdatatype` ".
// "type": "(.*)",      ||       (?<=\"type\": \").+?(?=")      using negative lookahead / lookbehind 
// "ptc": (.*),      ||       (?<=\"ptc\": ).+?(?=,)      using negative lookahead / lookbehind 
// "pfc": (.*)\}\}      ||       (?<=\"pfc\": ).+?(?=\})      using negative lookahead / lookbehind 
$sql = 
  "SELECT ".
  "    `id`, ".
  "    `domain`, ".
  "    `name`, ".
  "    `nativeType`, ".
  "    `desc`, ".
  "    `size`, ".
  "    `value`, ".
  "    concat('PTC/PFC: ', regexp_substr(setting, '(?<=\"ptc\": ).+?(?=,)'), '/', regexp_substr(setting, '(?<=\"pfc\": ).+?(?=\})')) AS `pusparamtype`, ".
  "    regexp_substr(setting, '(?<=\"type\": \").+?(?=\")') as `pusdatatype` ".
  "FROM `type` ".
  "WHERE idStandard = ".$idStandard." ".
  "ORDER BY domain, name ASC LIMIT $start_from, $num_rec_per_page"; 
} else {
// for MariaDB starting from the version 10.2.3
$sql = 
  "SELECT ".
  "    `id`, ".
  "    `domain`, ".
  "    `name`, ".
  "    `nativeType`, ".
  "    `desc`, ".
  "    `size`, ".
  "    `value`, ".
  "    concat('PTC/PFC: ', json_value(`setting`, '$.PUS.ptc'), '/', json_value(`setting`, '$.PUS.pfc')) AS `pusparamtype`, ".
  "    json_value(`setting`, '$.PUS.type') as `pusdatatype` ".
  "FROM `type` ".
  "WHERE idStandard = ".$idStandard." ".
  "ORDER BY domain, name ASC LIMIT $start_from, $num_rec_per_page"; 
}
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);

echo json_encode($data);

?>