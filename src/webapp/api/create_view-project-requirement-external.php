<?php

require 'db_config.php';

$post = $_POST;

// check if requirement id exists already

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

$sql = "SELECT * FROM `projectacronym` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>