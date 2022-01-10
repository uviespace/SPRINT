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
    // check, if category is already in database
    $sql_newCat = "SELECT * FROM `projectrequirementcategory` WHERE `idProject` = ".$post['idProject']." AND `category` = '".$reqCatName."'";
    $result_newCat = $mysqli->query($sql_newCat);
    $num_rows = mysqli_num_rows($result_newCat);
    if ($num_rows==0) {
        // if not in database, add it
        $sql_addCat = "INSERT INTO `projectrequirementcategory` ".
            "(`idProject`, `idSwComponent`, `category`, `shortDesc`) VALUES ".
            "(".$post['idProject'].", 2, '".$reqCatName."', 'inserted new category')";
        $result_addCat = $mysqli->query($sql_addCat);
    }
}

echo "reqCatName = $reqCatName";

$reqId = $reqCatName."-".$post['requirementNr']."/".$post['idReqType'];

if ($post['idReqVerif'] != '' && $post['idReqVerif'] != '-') {
    $reqId .= "/".$post['idReqVerif'];
}

echo "reqId = $reqId";

  // check if requirement already exists
  $sql = "SELECT * FROM `projectrequirement` WHERE `idProject` = ".$post['idProject']." AND `requirementId` = '".$reqId."'";
  $result = $mysqli->query($sql);
  $num_rows = mysqli_num_rows($result);
  if ($num_rows==0) {

    $sql = 
      "INSERT INTO ".
      "`projectrequirement` ".
      "(`idProject`, `idDocRelation`, `requirementId`, `shortDesc`, `desc`, `notes`) ".
      "VALUES ".
      "('".$post['idProject']."', 1, '".$reqId."', '".$post['shortDesc']."', '".$post['desc']."', '".$post['notes']."')";

    $result = $mysqli->query($sql);

    // get id
    $insert_id = $mysqli->insert_id;
    
    if ($post['newTLReqId'] != '') {
        
        // check, if top-level requirement is already in database
        $sql_newTLReq = "SELECT * FROM `projectrequirement` WHERE `idDocRelation` = 2 AND `requirementId` = '".$post['newTLReqId']."'";
        $result_newTLReq = $mysqli->query($sql_newTLReq);
        $num_rows = mysqli_num_rows($result_newTLReq);
        if ($num_rows==0) {
            // insert new top-level requirement
            $sql_addTLReq = "INSERT INTO `projectrequirement` (`idProject`, `idDocRelation`, `requirementId`, `desc`) VALUES (".$post['idProject'].", 2, '".$post['newTLReqId']."', '".$post['newTLReqDesc']."') ";
            $result_addTLReq = $mysqli->query($sql_addTLReq);

            // get id
            $insert_newTLReqId = $mysqli->insert_id;
        } else {
            $row_newTLReq = $result_newTLReq->fetch_assoc();
            $insert_newTLReqId = $row_newTLReq['id'];
        }
        
        // insert relation
        $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$insert_newTLReqId.", ".$insert_id.")";
        $result = $mysqli->query($sql);
        
    } else {
    
        if ($post['idTLReqId'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$post['idTLReqId'].", ".$insert_id.")";
            $result = $mysqli->query($sql);
        }
        if ($post['idTLReqId2'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$post['idTLReqId2'].", ".$insert_id.")";
            $result = $mysqli->query($sql);
        }
        if ($post['idTLReqId3'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$post['idTLReqId3'].", ".$insert_id.")";
            $result = $mysqli->query($sql);
        }

    }

  }

$sql = "SELECT * FROM `projectacronym` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>