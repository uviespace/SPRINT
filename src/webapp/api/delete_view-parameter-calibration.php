<?php


 require 'db_config.php';


 $id  = $_POST["id"];
 $post = $_POST;

 //$sql = "DELETE FROM `parameter` WHERE id = '".$id."'";
 $sql = "UPDATE `parameter` p SET p.setting = JSON_REMOVE(p.setting, '$.calcurve') WHERE id = ".$post["idParameter"]." ";
 //echo $sql;


 $result = $mysqli->query($sql);


 echo json_encode("{}");


?>