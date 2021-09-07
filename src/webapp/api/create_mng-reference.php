<?php

require 'db_config.php';

$post = $_POST;

 // check, if organisation already exists
 $sql = "SELECT * FROM `organisation` WHERE `name` = '".$post['organisation']."'";

 $result = $mysqli->query($sql);
 
 if ($result->num_rows > 0) {
     $row = $result->fetch_assoc();
     $idOrg = $row['id'];
 } else {
     $sql_insert = "INSERT INTO `organisation` (name) VALUES ('".$post['organisation']."')";
     $mysqli->query($sql_insert);
     $idOrg = $mysqli->insert_id;
 }
 
 $idDocRelation = "";
 
 // get short name from document type
 $sql = "SELECT * FROM `doctype` WHERE `id` = ".$post['type'];

 $result = $mysqli->query($sql);
 
 if ($result->num_rows > 0) {
     $row = $result->fetch_assoc();
     $docShortName = $row['name'];
 } else {
     $docShortName = "";
 }
 
 // check, if identifier already exists. 
 // if yes, get document ID
 // if no, add new document to `document` and get document ID of newly inserted document
 $sql = "SELECT d.id as docId, d.*, dv.* FROM `document` AS d, `docversion` AS dv WHERE dv.idDocument = d.id AND dv.identifier = ".$post['identifier'];
 
 $result = $mysqli->query($sql);
 
 if ($result->num_rows > 0) {
     // get document ID
     $row = $result->fetch_assoc();
     $idDocument = $row['docId'];
 } else {
     // add new document to `document`
     
     // get number from identifier (NOT NEEDED for extern references!)
     //$number = end(split('-',$post['identifier']));
     $number = "";
     
     $sql = 
       "INSERT INTO ".
       "`document` ".
       "(`idDocType`, `idDocRelation`, `idOrg`, `shortName`, `number`, `name`) ".
       "VALUES ".
       "('".$post['type']."','".$idDocRelation."','".$idOrg."','".$docShortName."','".$number."','".$post['name']."')";

     $result = $mysqli->query($sql);

     $idDocument = $mysqli->insert_id;
 }
 
 // check, if version already exists
 // if yes, do nothing and throw exception
 // if no, add new version to `docversion`
 $sql = "SELECT * FROM `docversion` WHERE idDocument = ".$idDocument;
 
 $result = $mysqli->query($sql);
 
 $found_version = false;
 if ($result->num_rows > 0) {
     while ($row = $result->fetch_assoc()) {
         if ($row['version']==$post['version']) {
             $found_version = true;
             break;
         }
     }
 } 
 
 if (!$found_version) {
     // add new version to `docversion`
 
     if ($post['note'] == "") {
         $note = "inserted as general reference";
     } else {
         $note = $post['note'];
     }
 
     $sql = 
       "INSERT INTO ".
       "`docversion` ".
       "(`idDocument`, `version`, `date`, `identifier`, `filename`, `note`) ".
       "VALUES ".
       "('".$idDocument."','".$post['version']."','".$post['date']."','".$post['identifier']."','".$post['filename']."','".$note."')";

     $result = $mysqli->query($sql);
 }

 $sql = "SELECT * FROM `document` ORDER BY id DESC LIMIT 1"; 

 $result = $mysqli->query($sql);

 $data = $result->fetch_assoc();

 echo json_encode($data);
 //header('Location: ../index.php');

?>