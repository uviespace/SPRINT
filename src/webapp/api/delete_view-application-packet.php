<?php


 require 'db_config.php';


 $id  = $_POST["id"];


if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };


 $sql = "DELETE FROM `applicationpacket` WHERE idApplication = '".$idApplication."' AND idStandard = '".$idStandard."' AND idPacket = '".$id."'";


 $result = $mysqli->query($sql);


 echo json_encode([$id]);


?>