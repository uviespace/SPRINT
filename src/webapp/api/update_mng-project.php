<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

  
  $sql = "UPDATE `project` SET `name` = '".$post['name']."', `desc` = '".$post['desc']."', `isPublic` = '".$post['isPublic']."', `setting` = '".$post['setting']."' WHERE `id` = '".$id."'";

  $result = $mysqli->query($sql);


  // search for user id
  $sql = "SELECT `id` FROM `user` WHERE `name` = '".$post['owner']."'";

  $result = $mysqli->query($sql);

  $user = $result->fetch_assoc();


  // update owner to new user id
  if (($user != null) && (sizeof($user) != 0)) {

    $sql = "UPDATE `userproject` SET `idUser` = '".$user["id"]."' WHERE `idProject` = '".$id."' AND `idRole` = '2'";

    $result = $mysqli->query($sql);

  }


  $sql = "SELECT * FROM `project` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>