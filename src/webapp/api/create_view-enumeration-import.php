<?php

require 'db_config.php';

if (isset($_GET["action"])) { $action  = $_GET["action"]; } else { $action=''; };
//if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
//if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["idType"])) { $idType  = $_GET["idType"]; } else { $idType=0; };

$post = $_POST;

if ($action == 'link') {
    $sql = "SELECT id FROM `acronym` WHERE `name` = '".$post['name']."' AND `shortDesc` = '".$post['shortDesc']."'";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    $row = $result->fetch_assoc();
    if ($num_rows==1) {
        $sql_link = "INSERT INTO `projectacronym` (`idProject`, `idAcronym`) VALUES (".$idProject.", ".$row['id'].")";
        echo $sql_link."<br/>";
        //$result_link = $mysqli->query($sql_link);
    }
} else {
    $sql = "INSERT INTO `enumeration` (`idType`, `name`, `value`, `desc`) VALUES (".$idType.", '".$post['name']."', ".$post['value'].", '".$post['desc']."')";
    $result = $mysqli->query($sql);
    //$acrId = $mysqli->insert_id;
    
    //$sql_link = "INSERT INTO `projectacronym` (`idProject`, `idAcronym`) VALUES (".$idProject.", ".$acrId.")";
    //$result_link = $mysqli->query($sql_link);
}

/*
$sql = "SELECT * FROM `parameter` WHERE idStandard = ".$post['idStandard']." AND domain = '".$post['domain']."' ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();
*/

//echo json_encode($data);
header('Location: ../index.php');

?>