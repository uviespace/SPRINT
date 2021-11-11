<?php

require 'db_config.php';

if (isset($_GET["action"])) { $action  = $_GET["action"]; } else { $action=''; };
if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idReqList"])) { $idReqList  = $_GET["idReqList"]; } else { $idReqList=0; };

if ($idReqList == 5) { // 11C
    $idDocVersion = 11;
} else if ($idReqList == 6) { // 40C 
    $idDocVersion = 10;
} else if ($idReqList == 7) { // 80C 
    $idDocVersion = 13;
} else if ($idReqList == 8) { // 41A [PUS-A]
    $idDocVersion = 9;
} else if ($idReqList == 9) { // 41C [PUS-C]
    $idDocVersion = 12;
} else {
    $idDocVersion = 0;
}

$post = $_POST;

if ($action == 'link') {
    $sql = "SELECT id FROM `acronym` WHERE `name` = '".$post['name']."' AND `shortDesc` = '".$post['shortDesc']."'";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    $row = $result->fetch_assoc();
    if ($num_rows==1) {
        $sql_link = "INSERT INTO `projectacronym` (`idProject`, `idAcronym`) VALUES (".$idProject.", ".$row['id'].")";
        $result_link = $mysqli->query($sql_link);
    }
} else {
    if ($idReqList>=4 && $idReqList<10) {
        $sql = 
          "INSERT INTO `requirement` ".
          "(`idDocVersion`, `clause`, `desc`) ".
          "VALUES ".
          "('".$idDocVersion."', '".$post['col01']."', '".$post['col02']."')";
    } else if ($idReqList==10) {
        $sql = 
          "INSERT INTO `projectrequirement` ".
          "(`idProject`, `idDocRelation`, `requirementId`, `shortDesc`, `desc`, `notes`, `justification`, `applicability`, `applicableToPayloads`) ".
          "VALUES ".
          "('".$idProject."', 2, '".$post['reqId']."', '', '".$post['reqText']."', '".$post['notes']."', '".$post['justification']."', '".$post['applicability']."', '".$post['applicableToPL']."')";
    } else {
        $sql = "SELECT * FROM `projectrequirement`";
    }
    $result = $mysqli->query($sql);
    
    if ($idReqList==10) {
        $reqId = $mysqli->insert_id;
        
        // search for ECSS Clause
        $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 11 AND `clause` = '".$post['ecssClause']."'";
        $result = $mysqli->query($sql_ecss);
        
        if (mysqli_num_rows($result)==1) {
            $row = $result->fetch_assoc();
            $sql_link = "INSERT INTO `requirementstandard` (`idProjectStandard`, `idProjectRequirement`) VALUES (".$row['id'].", ".$reqId.")";
            $result_link = $mysqli->query($sql_link);
        }
    } else if ($idReqList==11) {
        $reqId = $mysqli->insert_id;
        
        // search for ECSS Clause
        $sql_ecss = "SELECT * FROM `requirement` WHERE `idDocVersion` = 12 AND `clause` = '".$post['ecssClause']."'";
        $result = $mysqli->query($sql_ecss);
        
        if (mysqli_num_rows($result)==1) {
            $row = $result->fetch_assoc();
            $sql_link = "INSERT INTO `requirementstandard` (`idProjectStandard`, `idProjectRequirement`) VALUES (".$row['id'].", ".$reqId.")";
            $result_link = $mysqli->query($sql_link);
        }
    }

}

/*
$sql = "SELECT * FROM `parameter` WHERE idStandard = ".$post['idStandard']." AND domain = '".$post['domain']."' ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();
*/

//echo json_encode($data);
header('Location: ../index.php');

?>