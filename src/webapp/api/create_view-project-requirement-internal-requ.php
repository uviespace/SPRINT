<?php

require 'db_config.php';

$post = $_POST;

if ($post['idReqCat'] != '') {
    // get name of Requirement Category
    $sql_reqCat = "SELECT * FROM `projectrequirementcategory` WHERE `id`= ".$post['idReqCat'];
    $result_reqCat = $mysqli->query($sql_reqCat);
    $row_reqCat = $result_reqCat->fetch_assoc();
    $reqCatName = $row_reqCat['category'];
} else {
    $reqCatName = $post['newCat'];
    // TODO: check, if category is already in database
    // TODO: if not in database, add it
}

echo "reqCatName = $reqCatName";

$reqId = $reqCatName."-".$post['requirementNr']."/".$post['idReqType'];

if ($post['idReqVerif'] != '' && $post['idReqVerif'] != '-') {
    $reqId .= "/".$post['idReqVerif'];
}

echo "reqId = $reqId";


    $sql = 
      "INSERT INTO ".
      "`projectrequirement` ".
      "(`idProject`, `idDocRelation`, `requirementId`, `shortDesc`, `desc`) ".
      "VALUES ".
      "('".$post['idProject']."', 1, '".$reqId."', '".$post['shortDesc']."', '".$post['desc']."')";

    $result = $mysqli->query($sql);

    // get id
    $insert_id = $mysqli->insert_id;
    
    if ($post['idTLReqId'] != '') {
        
        // insert relation
        $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$post['idTLReqId'].", ".$insert_id.")";
        
        $result = $mysqli->query($sql);
        
    } else if ($post['newTLReqId'] != '') {
        
        // insert new Top-level requirement
        
        // get id
        
        // insert relation
        
    }



// check if requirement id exists already
/*
$sql = 
  "SELECT ".
  "* ".
  "FROM ".
  "`projectrequirement` ".
  "WHERE ".
  "`idProject` = ".$post['idProject']." AND ".
  "`idDocRelation` = 2 AND ".
  "`requirementId` = '".$post['requirementId']."' ";
  
$result = $mysqli->query($sql);
  
if ($result->num_rows == 0) {
    
    $sql = 
      "INSERT INTO ".
      "`projectrequirement` ".
      "(`idProject`, `idDocRelation`, `requirementId`) ".
      "VALUES ".
      "('".$post['idProject']."',2,'".$post['requirementId']."')";

    $result = $mysqli->query($sql);
    
    // get id
    $insert_id = $mysqli->insert_id;
    
    $sql = 
      "INSERT INTO ".
      "`requirementstandard` ".
      "(`idProjectStandard`, `idProjectRequirement`) ".
      "VALUES ".
      "('".$post['idRequirement_create']."','".$insert_id."')";

    $result = $mysqli->query($sql);

}
*/

$sql = "SELECT * FROM `projectacronym` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>