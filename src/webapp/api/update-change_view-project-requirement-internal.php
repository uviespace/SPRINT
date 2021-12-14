<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  $idTLReqId_change = $post['idTLReqId_change'];
  $idTLReqId2_change = $post['idTLReqId2_change'];
  $idTLReqId3_change = $post['idTLReqId3_change'];
  
  // check, if there are changes
  $sql = "SELECT * FROM `requirementrequirement` WHERE `idProjectRequirementInternal` = ".$id;
  $result = $mysqli->query($sql);
  $num_rows = mysqli_num_rows($result);
  if ($num_rows==0) {
      if ($idTLReqId_change!="") {
          $sql_insert = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId_change.", ".$id.")";
          $result_ins1 = $mysqli->query($sql_insert);
      }
      if ($idTLReqId2_change!="") {
          $sql_insert2 = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId2_change.", ".$id.")";
          $result_ins1 = $mysqli->query($sql_insert2);
      }
      if ($idTLReqId3_change!="") {
          $sql_insert3 = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId3_change.", ".$id.")";
          $result_ins1 = $mysqli->query($sql_insert3);
      }
  }
  if ($num_rows==1) {
      $row = $result->fetch_assoc();
      if ($idTLReqId_change!=$row["idProjectRequirementExternal"]) {
          $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
          $result_upd1 = $mysqli->query($sql_change);
      }
      if ($idTLReqId2_change!="") {
          $sql_insert2 = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId2_change.", ".$id.")";
          $result_ins1 = $mysqli->query($sql_insert2);
      }
      if ($idTLReqId3_change!="") {
          $sql_insert3 = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId3_change.", ".$id.")";
          $result_ins2 = $mysqli->query($sql_insert3);
      }
  }
  if ($num_rows==2) {
      $row = $result->fetch_assoc();
      if ($idTLReqId_change!=$row["idProjectRequirementExternal"]) {
          $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
          $result_upd1 = $mysqli->query($sql_change);
      }
      $row = $result->fetch_assoc();
      if ($idTLReqId2_change!=$row["idProjectRequirementExternal"]) {
          if ($idTLReqId2_change=="") {
              $sql_del = "DELETE FROM `requirementrequirement` WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_del1 = $mysqli->query($sql_del);
          } else {
              $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId2_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_upd2 = $mysqli->query($sql_change);
          }
      }
      if ($idTLReqId3_change!="") {
          $sql_insert3 = "INSERT INTO `requirementrequirement` (`idProjectRequirementExternal`, `idProjectRequirementInternal`) VALUES (".$idTLReqId3_change.", ".$id.")";
          $result_ins1 = $mysqli->query($sql_insert3);
      }
  }
  if ($num_rows==3) {
      $row = $result->fetch_assoc();
      if ($idTLReqId_change!=$row["idProjectRequirementExternal"]) {
          $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
          $result_upd1 = $mysqli->query($sql_change);
      }
      $row = $result->fetch_assoc();
      if ($idTLReqId2_change!=$row["idProjectRequirementExternal"]) {
          if ($idTLReqId2_change=="") {
              $sql_del = "DELETE FROM `requirementrequirement` WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_del1 = $mysqli->query($sql_del);
          } else {
              $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId2_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_upd2 = $mysqli->query($sql_change);
          }
      }
      $row = $result->fetch_assoc();
      if ($idTLReqId3_change!=$row["idProjectRequirementExternal"]) {
          if ($idTLReqId3_change=="") {
              $sql_del = "DELETE FROM `requirementrequirement` WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_del2 = $mysqli->query($sql_del);
          } else {
              $sql_change = "UPDATE `requirementrequirement` SET `idProjectRequirementExternal` = ".$idTLReqId3_change." WHERE `idProjectRequirementExternal` = ".$row["idProjectRequirementExternal"]." AND `idProjectRequirementInternal` = ".$id;
              $result_upd3 = $mysqli->query($sql_change);
          }
      }
  }


  $sql = "SELECT * FROM `projectrequirement` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>