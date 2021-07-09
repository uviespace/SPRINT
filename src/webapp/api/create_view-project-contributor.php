<?php

require 'db_config.php';

$post = $_POST;

$sql = 
      "INSERT INTO ".
      "`userproject` ".
      "(`idUser`, `idProject`, `idRole`, `email`) ".
      "VALUES ".
      "('".$post['idUser']."','".$post['idProject']."','".$post['idRole']."','".$post['email']."')";

 $result = $mysqli->query($sql);
 
 $sql = "SELECT * FROM `userproject` ORDER BY id DESC LIMIT 1"; 

 $result = $mysqli->query($sql);

 $data = $result->fetch_assoc();

 echo json_encode($data);
 //header('Location: ../index.php');

?>