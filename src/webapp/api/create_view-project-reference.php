<?php

require 'db_config.php';

$post = $_POST;

// get document ID from version ID
$sql = "SELECT DISTINCT * FROM `docversion` WHERE id = ".$post['idReference'];

$result = $mysqli->query($sql);

 if ($result->num_rows > 0) {
     $row = $result->fetch_assoc();
     $idDocument = $row['idDocument'];
 } else {
     $idDocument = "";
     // TODO: throw failure
 }

$sql = 
  "INSERT INTO ".
  "`projectdocument` ".
  "(`idProject`, `idDocument`) ".
  "VALUES ".
  "('".$post['idProject']."','".$idDocument."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `projectdocument` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

echo json_encode($data);
//header('Location: ../index.php');

?>