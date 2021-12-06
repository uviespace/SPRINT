<?php
session_start();
if(!isset($_SESSION['userid'])) {
    //die('Please <a href="login.php">login</a> first!');
    echo "Please <a href='login.php'>login</a> first!";
    echo "<br/><br/>";
    echo "<img src='img/loading.gif' />";
    header( "refresh:8;url=login.php" );
    die('');
}
require 'api/db_config.php';

if (isset($_GET["idProject"])) { $idProject  = $_GET["idProject"]; } else { $idProject=0; };
if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };
if (isset($_GET["idParent"])) { $idParent  = $_GET["idParent"]; } else { $idParent=0; };
if (isset($_GET["idPacket"])) { $idPacket  = $_GET["idPacket"]; } else { $idPacket=0; };
$project_name = "";
$standard_name = "";
$standard_desc = "";

$sql = "SELECT * FROM `project` WHERE `id` = ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $project_name = $row["name"];
    }
} else {
    //echo "0 results";
}

$sql = "SELECT * FROM `standard` WHERE `id` = ".$idStandard;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $standard_name = $row["name"];
        $standard_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

//if (isset($_GET["idParent"])) { $idParent  = $_GET["idParent"]; } else { $idParent=0; };
//if (isset($_GET["idPacket"])) { $idPacket  = $_GET["idPacket"]; } else { $idPacket=0; };

if ($idParent != 0) {
    $sql = "SELECT * FROM `packet` WHERE `id` = ".$idParent;
} else {
    $sql = "SELECT * FROM `packet` WHERE `id` = ".$idPacket;
    // get discriminant for TC/TM(st,sst,disc)
}

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $basePacket_name = $row["name"];
        $basePacket_desc = $row["desc"];
        $basePacket_kind = $row["kind"];
        $basePacket_type = $row["type"];
        $basePacket_subt = $row["subtype"];
    }
} else {
    //echo "0 results";
}

if ($basePacket_kind == 0) {
    $basePacket_kind_str = "TC";
} else if ($basePacket_kind == 1) {
    $basePacket_kind_str = "TM";
} else {
    $basePacket_kind_str = "n/a";
}

if ($idParent != 0) {
    $sql = "SELECT * FROM `packet` WHERE `id` = ".$idPacket;
    
    $result = $mysqli->query($sql);

    $num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $derivedPacket_discr = $row["discriminant"];
    }
} else {
    //echo "0 results";
}
    $basePacket_name .= " [".$derivedPacket_discr."]";
} else {
    $derivedPacket_discr = "";
}

function get_header_len($mysqli, $standard_id, $header_type) {
    
    $sql = "SELECT p.* FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND (p.kind = 1 OR p.kind = 0) AND ps.type = ".$header_type." AND ps.idStandard = ".$standard_id;
    
    $result = $mysqli->query($sql);

    $num_rows = mysqli_num_rows($result);

    if ($result->num_rows > 0) {
        
        $calc_size = 0;
        
        while ($row = $result->fetch_assoc()) {
            
            //echo "Parameter: ".$row['name']."<br/>";
            
            if ($row['domain'] == 'predefined') {
                
                $add_size = $row['size'];
                
            } else if ($row['idType'] >= 101 AND $row['idType'] < 200) {
                
                $add_size = $row['size'];
                
            } else {
                
                $sql_param_size = "SELECT size FROM `type` WHERE id = ".$row['idType'];
                
                $result_param_size = $mysqli->query($sql_param_size);
                
                $num_rows_param_size = mysqli_num_rows($result_param_size);
                
                $row_param_size = $result_param_size->fetch_assoc();
                
                $add_size = $row_param_size['size'];
                
            }
            
            if (isset($row['multiplicity']) AND $row['multiplicity'] > 1) {
                $add_size = $add_size*$row['multiplicity'];
            }
            
            //echo $add_size."<br/>";
            
            $calc_size += $add_size;
            
        }
        
    }
    
    return $calc_size/8;
}

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

//Abfrage der Rolle des Users
$sql = "SELECT * FROM userproject WHERE idProject = ".$idProject." AND (idUser = ".$userid." OR email = '".$userEmail."')";
$result = $mysqli->query($sql);
$idRole = 5;
while ($row = $result->fetch_assoc()) {
    $idRoleRead = $row["idRole"];
    if ($idRoleRead < $idRole) { $idRole = $idRoleRead; };
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Packet Inspector</title>
	<!-- https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css -->
	<link rel="stylesheet" type="text/css" href="ext/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.js -->
	<script type="text/javascript" src="ext/ajax/libs/jquery/3.1.0/jquery.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js -->
	<script src="ext/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css -->
	<link href="ext/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
	<!-- https://github.com/knownasilya/jquery-highlight -->
	<script type="text/javascript" src="ext/jquery.highlite.js"></script>
	<link rel="stylesheet" type="text/css" href="int/layout.css">
    <script type="text/javascript" src="int/config.js"></script>
	<script type="text/javascript" src="int/livesearch.js"></script>
	<script type="text/javascript" src="js/item-ajax_view-packet-params-derived.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Packet Inspector for Packet <?php echo $basePacket_kind_str ."(".$basePacket_type."/".$basePacket_subt.") " . $basePacket_name; ?></h2>
		        </div>
		        <div class="pull-right">
				<!--<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>-->
		        </div>
		    </div>
		</div>
		
		<br/><br/>
		
<?php
/*
$fields = array();
$fields[] = "6B";
$fields[] = "4b";
$fields[] = "3b";
$fields[] = "1b";
$fields[] = "1B";
$fields[] = "2B";
$fields[] = "1B";
$fields[] = "1B";
$fields[] = "1B";
$fields[] = "2B";

foreach ($fields as &$field) {

    switch ($field) {
        case "1b":
            echo "<div style='width:5px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "2b":
            echo "<div style='width:10px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "3b":
            echo "<div style='width:15px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "4b":
            echo "<div style='width:20px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "1B":
            echo "<div style='width:40px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "2B":
            echo "<div style='width:80px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "4B":
            echo "<div style='width:160px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
        case "6B":
            echo "<div style='width:240px; height:40px; border:1px solid black; display: inline-block;'>".$field."</div>";
            break;
    }

}
*/

echo "<br/><br/>";

$debug = false;
$scale = 5; // scale = 5: 1b = 5px; 1B = 40px
$nbGroup = 0;

// ### HEADER ###

/*$header_type = $basePacket_kind; // TC: 0 ; TM: 1
$header_len = get_header_len($mysqli, $idStandard, $header_type);
echo "Calculated Header Length: ".$header_len." Bytes<br/><br/>";*/

if ($basePacket_kind == 0) {
    //$basePacket_kind_str = "TC";
    $field_len_B = get_header_len($mysqli, $idStandard, $basePacket_kind); // =10 Bytes (PUS-A)
    $field_len = $field_len_B * $scale * 8;
    echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#6495ED; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>TC Header</b><br/>(".strval($field_len_B)."B)</div>";
} else if ($basePacket_kind == 1) {
    //$basePacket_kind_str = "TM";
    $field_len_B = get_header_len($mysqli, $idStandard, $basePacket_kind); // =18 Bytes (PUS-A)
    $field_len = $field_len_B * $scale * 8;
    echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#6495ED; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>TM Header</b><br/>(".strval($field_len_B)."B)</div>";
} else {
    //$basePacket_kind_str = "n/a";
    $field_len_B = 6; // Bytes
    $field_len = $field_len_B * $scale * 8;
    echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#6495ED; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>Unknown Header</b><br/>(".strval($field_len_B)."B)</div>";
}

$header_len = $field_len_B;

// ### DATA ###

if ($idParent != 0) {

$sql = 
  "SELECT ".
  "ps.id, ps.type, ps.idParameter, ps.order, ps.role, ps.group, ps.repetition, ps.desc, p.name, p.idType, t.size ".
  "FROM `parametersequence` AS ps, `parameter` AS p, `type` AS t ".
  "WHERE ps.idPacket = ".$idParent." AND ps.idParameter = p.id AND p.idType = t.id ".
  "ORDER BY ps.order ASC";

} else {

$sql = 
  "SELECT ".
  "ps.id, ps.type, ps.idParameter, ps.order, ps.role, ps.group, ps.repetition, ps.desc, p.name, p.idType, t.size ".
  "FROM `parametersequence` AS ps, `parameter` AS p, `type` AS t ".
  "WHERE ps.idPacket = ".$idPacket." AND ps.idParameter = p.id AND p.idType = t.id ".
  "ORDER BY ps.order ASC";

}

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

$data_len = 0;
if ($result->num_rows > 0) {
    // output data of each row
    if ($debug) {
        echo "<br/>";
    }
    $group = 0;
    $rep=0;
    $nbRep=0;
    while($row = $result->fetch_assoc()) {
        if ($debug) {
            echo "<br/>";
            echo "id: " . $row["id"]. " - ".
                 "Type: " . $row["type"]. " - ".
                 "Parameter ID: " . $row["idParameter"]. " - ".
                 "Order: " . $row["order"]. " - ".
                 "Description: " . $row["desc"]." - ".
                 "Name: " . $row["name"]." - ".
                 "Role: " . $row["role"]." - ".
                 "Group: " . $row["group"]. " - ".
                 "Repetition: " . $row["repetition"]. " - ".
                 "idType: " . $row["idType"]. " - ".
                 "size: " . $row["size"]." bits<br/>";
        }
        $field_len = $row["size"] * $scale; // bits * px/bit
        if ($row["role"] == 3) { // Discriminant
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#EEDD82; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        } else if ($row["role"] == 8) { // Spare 
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#FFFFFF; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        } else if ($row["group"]!="") { // Group
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#F9D3B6; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        } else if ($group > 0 AND $group <= $nbGroup) { // parameter in group
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#F4E7D9; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        } else {
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#FAFAD2; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        }
        // collect data of group parameters
        if ($group > 0 AND $group <= $nbGroup) {
          $name[$group-1] = $row["name"];
          $size[$group-1] = strval($row["size"]/8);
          //echo $group.". parameter: ".$name[$group-1]." (".$size[$group-1].")";
          $group += 1;
        }
        // repeat parameter sequence of group 
        if ($group > $nbGroup AND $rep != "" AND $rep>0) {
          echo "<br/>";
          for ($i=0; $i<$nbRep-1; $i++) {
            for ($j=0; $j<$nbGroup; $j++) {
              //echo $name[$j]." (".$size[$j]." )<br/>";
              $field_len = $size[$j] * 8 * $scale; // bits * px/bit
              echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#F4E7D9; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$name[$j]."</b><br/>(".$size[$j]."B)</div>";
              $data_len += $size[$j] * 8;
            }
          }
          $group = 0;
          $nbGroup = 0;
          $nbRep = 0;
        }
        if ($row["group"]!="") {
          $nbGroup = $row["group"];
          //echo "<br/>";
          //echo "Nb of Group: ".$nbGroup;
          if ($row["repetition"]!="") {
            $nbRep = $row["repetition"];
            //echo "<br/>";
            //echo "Repetions: ".$nbRep;
            //echo "<br/>";
          }
          $group = 1;
          $rep = $nbRep;
        }

        $data_len += $row["size"];
    }
} else {
    if ($debug) {
        echo "0 results";
    }
}

/*echo "<br/>";*/

if ($idParent != 0) {
$sql = 
  "SELECT ".
  "ps.id, ps.type, ps.idParameter, ps.order, ps.role, ps.desc, p.name, p.idType, t.size ".
  "FROM `parametersequence` AS ps, `parameter` AS p, `type` AS t ".
  "WHERE ps.idPacket = ".$idPacket." AND ps.idParameter = p.id AND p.idType = t.id ".
  "ORDER BY ps.order ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    if ($debug) {
        echo "<br/>";
    }
    while($row = $result->fetch_assoc()) {
        if ($debug) {
            echo "<br/>";
            echo "id: " . $row["id"]. " - ".
                 "Type: " . $row["type"]. " - ".
                 "Parameter ID: " . $row["idParameter"]. " - ".
                 "Order: " . $row["order"]. " - ".
                 "Description: " . $row["desc"]." - ".
                 "Name: " . $row["name"]." - ".
                 "Role: " . $row["role"]." - ".
                 "Group: " . $row["group"]. " - ".
                 "Repetition: " . $row["repetition"]. " - ".
                 "idType: " . $row["idType"]. " - ".
                 "size: " . $row["size"]." bits<br/>";
        }
        $field_len = $row["size"] * $scale; // bits * px/bit
        if ($row["role"] == 8) { // Spare 
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#F3FFF3; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        } else {
            echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#8FBC8F; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>".$row["name"]."</b><br/>(".strval($row["size"]/8)."B)</div>";
        }
        $data_len += $row["size"];
    }
} else {
    if ($debug) {
        echo "0 results";
    }
}

}
$data_len = $data_len/8;

// ### CRC ###

if ($debug) {
    echo "<br/>";
}

$field_len_B = 2; // Bytes
$field_len = $field_len_B * $scale * 8;
$crc_len = $field_len_B;
echo "<div style='width:".strval($field_len)."px; height:42px; background-color:#FFA500; border:1px solid black; padding-left:2px; font-size: x-small; display: inline-block;'><b>CRC</b><br/>(".strval($field_len_B)."B)</div>";

$total_len = $header_len + $data_len + $crc_len;

// ###########

echo "<br/><br/>";

echo "<b>Header Length:</b> ".$header_len." Bytes<br/>";
echo "<b>Data Length:</b> ".$data_len." Bytes<br/>";
echo "<b>CRC Length:</b> ".$crc_len." Bytes<br/>";
echo "<b>Total Length:</b> ".$total_len." Bytes<br/>";

echo "<br/><br/>";
echo "<br/><br/>";
?>


				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<!-- sel_parameter-derived.php -->
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>