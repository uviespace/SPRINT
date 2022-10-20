<?php


  require 'db_config.php';


  $id  = $_POST["id"];
  $idApplication  = $_POST["idApplication"];
  $idStandard  = $_POST["idStandard"];

  $post = $_POST;


  $sql = 
    "UPDATE ".
    "`applicationpacket` ".
    "SET ".
    "`cmdUsrCheckEnable` = '".$post['enablecheck']."', ".
    "`cmdUsrCheckReady` = '".$post['readycheck']."', ".
    "`cmdUsrCheckRepeat` = '".$post['repeatcheck']."', ".
    "`cmdUsrActionUpdate` = '".$post['updateaction']."' ".
    "WHERE ".
    "`idApplication` = '".$idApplication."' AND ".
    "`idStandard` = '".$idStandard."' AND ".
    "`idPacket` = '".$id."'";


  $result = $mysqli->query($sql);


  $sql = 
    "SELECT ".
    "* ".
    "FROM ".
    "`applicationpacket` ".
    "WHERE ".
    "`idApplication` = '".$idApplication."' AND ".
    "`idStandard` = '".$idStandard."' AND ".
    "`idPacket` = '".$id."'"; 


  $result = $mysqli->query($sql);


  $data = $result->fetch_assoc();


  echo json_encode($data);


?>