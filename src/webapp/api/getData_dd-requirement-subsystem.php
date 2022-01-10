<?php

require 'db_config.php';

$num_rec_per_page = 5;

/*if (isset($_GET["page"])) { $page = $_GET["page"]; } else { $page=1; };*/
//if (isset($_GET["id"])) { $id = $_GET["id"]; } else { $id=0; };
if (isset($_GET["idProject"])) { $idProject = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idReq"])) { $idReq = $_GET["idReq"]; } else { $idReq=0; };

/*$start_from = ($page-1) * $num_rec_per_page;*/

//if ($id==0 ) {
if ($idProject==0) {
$sql = "SELECT ".
  "pr.id AS id, r.id AS idAcronym, pr.*, r.clause, r.idDocVersion ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementstandard` AS rs, `requirement` AS r ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rs.idProjectRequirement = pr.id AND ".
  "rs.idProjectStandard = r.id ".
  "ORDER BY pr.requirementId ASC";
} else {
$sql = "SELECT ".
  "pr.id AS id, r.id AS idAcronym, pr.*, r.clause, r.idDocVersion ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementstandard` AS rs, `requirement` AS r ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "rs.idProjectRequirement = pr.id AND ".
  "rs.idProjectStandard = r.id AND ".
//  "r.idDocVersion = ".$idDocVersion." AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC";
}
//} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
//$sql = "SELECT * FROM `projectrequirementcategory` WHERE `idProject` = ".$idProject." AND `id` = ".$id." ORDER BY `category` ASC"; 
//}
if ($idReq==0) {
$sql = "SELECT ".
  "* ".
  "FROM ".
  "`projectrequirement` AS pr ".
  "WHERE ".
  "pr.idDocRelation = 1 AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC";
} else {
$sql = "SELECT ".
  "pr.* ".
  "FROM ".
  "`projectrequirement` AS pr, `requirementrequirement` AS rr ".
  "WHERE ".
  "pr.id = rr.idProjectRequirementInternal AND ".
  "rr.idProjectRequirementExternal = ".$idReq." AND ".
  "pr.idDocRelation = 1 AND ".
  "pr.idProject = ".$idProject." ".
  "ORDER BY pr.requirementId ASC";
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

/*$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);*/

echo json_encode($data);

?>