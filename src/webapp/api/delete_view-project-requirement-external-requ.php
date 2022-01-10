<?php


 require 'db_config.php';


 $id  = $_POST["id"];


 $sql = "DELETE FROM `projectrequirement` WHERE id = '".$id."'";


 $result = $mysqli->query($sql);


 $sql = "DELETE FROM `requirementrequirement` WHERE idProjectRequirementExternal = '".$id."'";


 $result = $mysqli->query($sql);
 

 echo json_encode([$id]);


?>