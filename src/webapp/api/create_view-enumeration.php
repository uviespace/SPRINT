<?php

require 'db_config.php';

$post = $_POST;

$sql = "INSERT INTO `enumeration` (`idType`, `name`, `value`, `desc`) VALUES ('".$post['idType']."','".$post['name']."','".$post['value']."','".$post['desc']."')";

$result = $mysqli->query($sql);

$sql = "SELECT * FROM `enumeration` ORDER BY id DESC LIMIT 1"; 

$result = $mysqli->query($sql);

$data = $result->fetch_assoc();

//echo json_encode($data);
header('Location: ../index.php');

?>