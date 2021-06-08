<?php


 require 'db_config.php';


 $id  = $_POST["id"];

  // TODO: check dependencies for this standard 

 $sql = "DELETE FROM `standard` WHERE id = '".$id."'";


 $result = $mysqli->query($sql);


 echo json_encode([$id]);


?>