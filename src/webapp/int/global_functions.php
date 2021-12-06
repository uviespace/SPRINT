<?php
/**
 * GLOBAL FUNCTIONS
 */

/*
//Abfrage der Rolle des Users
$sql = "SELECT * FROM userproject WHERE idProject = ".$idProject." AND (idUser = ".$userid." OR email = '".$userEmail."')";
$result = $mysqli->query($sql);
$idRole = 5;
while ($row = $result->fetch_assoc()) {
    $idRoleRead = $row["idRole"];
    if ($idRoleRead < $idRole) { $idRole = $idRoleRead; };
}
*/

function get_max_access_level($mysqli, $projectid, $userid, $userEmail) {
   // get access level 
   $sql = "SELECT * FROM `userproject` WHERE ".
          "`idProject` = ".$projectid." AND ".
          "(".
          "(`idUser` = ".$userid." AND (`idRole` = 1 OR `idRole` = 2)) OR ".
          "(`email` = '".$userEmail."' AND (`idRole` = 3 OR `idRole` = 4))".
          ")";
   $result = $mysqli->query($sql);
   
   $max_acc_lev = 5;
   while($row = $result->fetch_assoc()){
       if ($row['idRole'] < $max_acc_lev) {
           $max_acc_lev = $row['idRole'];
       }
   }

    return $max_acc_lev;
}

?>