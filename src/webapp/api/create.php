<?php

require 'db_config.php';

$post = $_POST;

$sql = "INSERT INTO `limit` (id, idParameter, type, lvalue, hvalue, setting) VALUES ('".$post['id']."','".$post['idParameter']."','".$post['type']."','".$post['lvalue']."','".$post['hvalue']."','".$post['setting']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `limit` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>