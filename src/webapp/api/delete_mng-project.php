<?php


 require 'db_config.php';


 $id  = $_POST["id"];


  // check if project has already defined application and/or standard
  // if yes, do nothoing!

 $sql = "SELECT * FROM `application` WHERE idProject = '".$id."'";
 $result = $mysqli->query($sql);
 $row_cnt_app = $result->num_rows;

 if ($row_cnt_app == 0) {

 $sql = "SELECT * FROM `standard` WHERE idProject = '".$id."'";
 $result = $mysqli->query($sql);
 $row_cnt_std = $result->num_rows;

 if ($row_cnt_std == 0) {

     // delete project 

     $sql = "DELETE FROM `project` WHERE id = '".$id."'";

     $result = $mysqli->query($sql);

     // delete owner (maintainer) and associated contributors or guests 

     $sql = "DELETE FROM `userproject` WHERE idProject = '".$id."'";

     $result = $mysqli->query($sql);

     echo json_encode([$id]);
 
 } else {
     $response["status"] = 2002; //Make up your own error codes! Yippee! Fun!
     $response["statusText"] = "Error: There are existing standards for this project!";
     echo json_encode($response);
 }
 
 } else {
     $response["status"] = 2001; //Make up your own error codes! Yippee! Fun!
     $response["statusText"] = "Error: There are existing applications for this project!";
     echo json_encode($response);
 }


?>