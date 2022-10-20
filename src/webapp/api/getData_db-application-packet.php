<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["idApplication"])) { $idApplication  = $_GET["idApplication"]; } else { $idApplication=1; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=1; };
if (isset($_GET["idPacket"])) { $idPacket  = $_GET["idPacket"]; } else { $idPacket=1; };
if (isset($_GET["kind"])) { $kind  = $_GET["kind"]; } else { $kind=0; };

$start_from = ($page-1) * $num_rec_per_page;

if ($idStandard==0) {
/*$sqlTotal = "SELECT * FROM `service`";*/
$sql = 
  "SELECT ".
  "* ".
  "FROM ".
  "`applicationpacket` ".
  "ORDER BY `type` ASC"; 
} else {
/*$sqlTotal = "SELECT * FROM `service`";*/
if ($kind==1) { // TM packet
$sql = 
  "SELECT ".
  "`repPrvCheckEnable`, ".
  "`repPrvCheckReady`, ".
  "`repPrvCheckRepeat`, ".
  "`repUsrCheckAcceptance`, ".
  "`repPrvActionUpdate`, ".
  "`repUsrActionUpdate` ".
  "FROM ".
  "`applicationpacket` ".
  "WHERE ".
  "idApplication = ".$idApplication." AND ".
  "idStandard = ".$idStandard." AND ".
  "idPacket = ".$idPacket." ";
  "ORDER BY `idPacket` ASC"; 
} else { // TC packet
$sql = 
  "SELECT ".
  "`cmdUsrCheckEnable`, ".
  "`cmdUsrCheckReady`, ".
  "`cmdUsrCheckRepeat`, ".
  "`cmdPrvCheckAcceptance`, ".
  "`cmdPrvCheckReady`, ".
  "`cmdUsrActionUpdate`, ".
  "`cmdPrvActionStart`, ".
  "`cmdPrvActionProgress`, ".
  "`cmdPrvActionTermination`, ".
  "`cmdPrvActionAbort` ".
  "FROM ".
  "`applicationpacket` ".
  "WHERE ".
  "idApplication = ".$idApplication." AND ".
  "idStandard = ".$idStandard." AND ".
  "idPacket = ".$idPacket." ".
  "ORDER BY `idPacket` ASC"; 
}
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

/*$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);*/

echo json_encode($data);

?>