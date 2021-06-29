<?php


 require 'db_config.php';


 $id  = $_POST["id"];


 // check if user has already been referenced in projects as admin, maintainer or contributor
 // if yes, do nothoing!

 $sql = "SELECT * FROM `userproject` WHERE idUser = '".$id."' AND (idRole = 1 OR idRole = 2 OR idRole = 3)";
 $result = $mysqli->query($sql);
 $row_cnt_ref = $result->num_rows;
 
 // check if user is not referenced to a project or only referenced as guest
 // if yes, delete 

 if ($row_cnt_ref == 0) {

     $sql = "SELECT * FROM `userproject` WHERE idUser = '".$id."' AND idRole = 4";
     $result = $mysqli->query($sql);
     $row_cnt_guest = $result->num_rows;

     if ($row_cnt_guest != 0) {

         // delete associated guests 

         $sql = "DELETE FROM `userproject` WHERE idUser = '".$id."' AND idRole = 4";

         $result = $mysqli->query($sql);

     }

     // delete user 

     $sql = "DELETE FROM `user` WHERE id = '".$id."'";

     $result = $mysqli->query($sql);

     echo json_encode([$id]);

 } else {
     $response["status"] = 1001; //Make up your own error codes! Yippee! Fun!
     $response["statusText"] = "Error! There are existing references to projects for this user!";
     echo json_encode($response);
 }

?>