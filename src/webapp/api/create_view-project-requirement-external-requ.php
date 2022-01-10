<?php

require 'db_config.php';

$post = $_POST;

$reqId = $post['reqId'];

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
      "('".$post['idProject']."', 2, '".$reqId."', '".$post['shortDesc']."', '".$post['desc']."', '".$post['notes']."')";

    $result = $mysqli->query($sql);

    // get id
    $insert_id = $mysqli->insert_id;
    
    if ($post['newTLReqId'] != '') {
        
        // check, if subsystem requirement is already in database
        $sql_newTLReq = "SELECT * FROM `projectrequirement` WHERE `idDocRelation` = 1 AND `requirementId` = '".$post['newTLReqId']."'";
        $result_newTLReq = $mysqli->query($sql_newTLReq);
        $num_rows = mysqli_num_rows($result_newTLReq);
        if ($num_rows==0) {
            // insert new subsystem requirement
            $sql_addTLReq = "INSERT INTO `projectrequirement` (`idProject`, `idDocRelation`, `requirementId`, `desc`) VALUES (".$post['idProject'].", 1, '".$post['newTLReqId']."', '".$post['newTLReqDesc']."') ";
            $result_addTLReq = $mysqli->query($sql_addTLReq);

            // get id
            $insert_newTLReqId = $mysqli->insert_id;
        } else {
            $row_newTLReq = $result_newTLReq->fetch_assoc();
            $insert_newTLReqId = $row_newTLReq['id'];
        }
        
        // insert relation
        $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementInternal`, `idProjectRequirementExternal`) VALUES (".$insert_newTLReqId.", ".$insert_id.")";
        $result = $mysqli->query($sql);
        
    } else {
    
        if ($post['idTLReqId'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementInternal`, `idProjectRequirementExternal`) VALUES (".$post['idTLReqId'].", ".$insert_id.")";
            $result = $mysqli->query($sql);
        }
        if ($post['idTLReqId2'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementInternal`, `idProjectRequirementExternal`) VALUES (".$post['idTLReqId2'].", ".$insert_id.")";
            $result = $mysqli->query($sql);
        }
        if ($post['idTLReqId3'] != '') {
            // insert relation
            $sql = "INSERT INTO `requirementrequirement` (`idProjectRequirementInternal`, `idProjectRequirementExternal`) VALUES (".$post['idTLReqId3'].", ".$insert_id.")";
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