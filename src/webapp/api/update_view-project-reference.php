<?php


  require 'db_config.php';


  $id  = $_POST["id"];

  $post = $_POST;

// get document ID from version ID
$sql = "SELECT * FROM `docversion` WHERE id = ".$post['idReference'];

$result = $mysqli->query($sql);

 if ($result->num_rows > 0) {
     $row = $result->fetch_assoc();
     $idDocument = $row['idDocument'];
 } else {
     $idDocument = "";
     // TODO: throw failure
 }
  
  $sql = "UPDATE `projectdocument` SET `idDocument` = '".$idDocument."' WHERE `id` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `projectdocument` WHERE `id` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>