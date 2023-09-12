<?php


  require 'db_config.php';

  if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };

  $id  = $_POST["id"];

  $post = $_POST;
  
  print_r($post);

/*
if ($idProject==0) {

  $sql = 
  "UPDATE `teststep` ".
  "SET ".
  "`testStepId` = '".$post['testStep']."', ".
  "`shortDesc` = '".$post['shortDescTestStep']."', ".
  "`desc` = '".$post['descTestStep']."', ".
  "`notes` = '".$post['notes']."' ".
  "WHERE ".
  "`id` = '".$id."'";

} else {

  // get Test ID
  $sql = "SELECT teststep.idTest FROM teststep, test WHERE test.testId = '".$post['name']."' AND test.id = teststep.idTest AND teststep.testStepId = '".$post['testStep']."' AND teststep.shortDesc LIKE '%".$post['shortDescTestStep']."%'";
  echo "sql: ".$sql;

  $result = $mysqli->query($sql);
  
  if (mysqli_num_rows($result)>0) {
	  
    $row = $result->fetch_assoc();
    $idTest = $row["idTest"];
    
    $sql = 
    "UPDATE `test` ".
    "SET ".
    "`testId` = '".$post['name']."', ".
    "`shortDesc` = '".$post['shortDesc']."', ".
    "`desc` = '".$post['desc']."' ".
    "WHERE ".
    "`id` = ".$idTest;
	
  }

}

  $result = $mysqli->query($sql);


  $sql = "SELECT * FROM `teststep` WHERE `id` = ".$id; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);
*/

?>