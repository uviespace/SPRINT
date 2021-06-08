<?php


 require 'db_config.php';


 $id  = $_POST["id"];

  // TODO: check dependencies for this application 

 $sql = "DELETE FROM `application` WHERE id = '".$id."'";


 $result = $mysqli->query($sql);


 echo json_encode([$id]);


?>