<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["reqCat"])) { $reqCat  = $_GET["reqCat"]; } else { $reqCat=0; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($idProject==0) {
$sqlTotal = "SELECT * FROM `projectrequirement`";
$sql = "SELECT * FROM `projectrequirement` ORDER BY `id` ASC LIMIT $start_from, $num_rec_per_page";
} else {
if ($reqCat!=0) {
// get reqCat
$sql_reqCat = "SELECT * FROM `projectrequirementcategory` WHERE `idProject` = ".$idProject." AND `id`= ".$reqCat;
$result = $mysqli->query($sql_reqCat);
$row = $result->fetch_assoc();
$reqCatName = $row['category'];

$sqlTotal = "SELECT DISTINCT ".
  "pr.id AS id, pr.*, (SELECT GROUP_CONCAT(spr.requirementId) FROM `projectrequirement` AS spr, `requirementrequirement` AS srr WHERE spr.id = srr.idProjectRequirementExternal AND srr.idProjectRequirementInternal = pr.id AND spr.idDocRelation = 2) AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.requirementId LIKE '".$reqCatName."%' AND ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ";
$sql = "SELECT DISTINCT ".
  "pr.id AS id, pr.*, (SELECT GROUP_CONCAT(spr.requirementId) FROM `projectrequirement` AS spr, `requirementrequirement` AS srr WHERE spr.id = srr.idProjectRequirementExternal AND srr.idProjectRequirementInternal = pr.id AND spr.idDocRelation = 2) AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.requirementId LIKE '".$reqCatName."%' AND ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC LIMIT $start_from, $num_rec_per_page";

/*$sqlTotal = "SELECT ".
  "pr.id AS id, pr.*, rr.idProjectRequirementExternal AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ";
$sql = "SELECT ".
  "pr.id AS id, pr.*, rr.idProjectRequirementExternal AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC LIMIT $start_from, $num_rec_per_page";*/
} else {
$sqlTotal = "SELECT DISTINCT ".
  "pr.id AS id, pr.*, (SELECT GROUP_CONCAT(spr.requirementId) FROM `projectrequirement` AS spr, `requirementrequirement` AS srr WHERE spr.id = srr.idProjectRequirementExternal AND srr.idProjectRequirementInternal = pr.id AND spr.idDocRelation = 2) AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ";
$sql = "SELECT DISTINCT ".
  "pr.id AS id, pr.*, (SELECT GROUP_CONCAT(spr.requirementId) FROM `projectrequirement` AS spr, `requirementrequirement` AS srr WHERE spr.id = srr.idProjectRequirementExternal AND srr.idProjectRequirementInternal = pr.id AND spr.idDocRelation = 2) AS clause ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rr.idProjectRequirementInternal = pr.id AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC LIMIT $start_from, $num_rec_per_page";
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