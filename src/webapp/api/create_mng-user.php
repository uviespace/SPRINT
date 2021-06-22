<?php

require 'db_config.php';

$post = $_POST;

 // check if user with this email already exists

 $sql = "SELECT * FROM `user` WHERE `email` = '".$post['email']."'";
 
 $result = $mysqli->query($sql);
 
 $row_cnt_user = $result->num_rows;
 
 if ($row_cnt_user == 0) {

     $defaultPassword = "e10adc3949ba59abbe56e057f20f883e";
     $signedUp = date('Y-m-d G:i:s');
     $defaultSetting = '{"status":"active"}';

     $sql = 
      "INSERT INTO ".
      "`user` ".
      "(`name`, `email`, `password`, `signedUp`, `setting`) ".
      "VALUES ".
      "('".$post['name']."','".$post['email']."','".$defaultPassword."','".$signedUp."','".$defaultSetting."')";

     $result = $mysqli->query($sql);

 }
 
 $sql = "SELECT * FROM `user` ORDER BY id DESC LIMIT 1"; 

 $result = $mysqli->query($sql);

 $data = $result->fetch_assoc();

 //echo json_encode($data);
 header('Location: ../index.php');

?>