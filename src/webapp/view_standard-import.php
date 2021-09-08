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

function get_sub_types($mysqli, $std, $typ, $servid, $checked) {

    $array = array();

    $sql = "SELECT * FROM `packet` WHERE `idStandard` = ".$std." AND `type`= ".$typ;

    $result = $mysqli->query($sql);

    $num_rows = mysqli_num_rows($result);

    // TODO: look for derived packets

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            //echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            if (in_array($row["id"], $checked) OR empty($checked)) {
            //if ($checked) {
                echo "<input type='checkbox' name='sel_service_subtype[".$servid."][]' value='".$row["id"]."' onchange='form.submit()' checked> (".$row["type"].",".$row["subtype"].",".$row["discriminant"].") ".$row["name"]."</input><br/>";
                $array[] = $row["id"];
            } else {
                echo "<input type='checkbox' name='sel_service_subtype[".$servid."][]' value='".$row["id"]."' onchange='form.submit()'> (".$row["type"].",".$row["subtype"].",".$row["discriminant"].") ".$row["name"]."</input><br/>";
            }
            //$project_name = $row["name"];
        }
    } else {
        //echo "0 results";
    }

    return $array;

}

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET Editor - Import to Standard</title>
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
	<script type="text/javascript" src="js/item-ajax_view-tmheader.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Standard <?php echo $standard_name;?></h4>
		            <h2>Import to Standard</h2>
		        </div>
		        <div class="pull-right">
				<!--<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>-->
		        </div>
		    </div>
		</div>

		<div>
		<form action="view_standard-import.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" method="post">
			<b>Select Project:</b>
<?php
// TODO: select only project's the current user should have access (not available for guests!)
$sql = "SELECT * FROM `project` WHERE `id` <> ".$idProject;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    echo "<select name='sel_project' onchange='form.submit()'>";
    echo "<option value='-'>Please select project ...</option>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        if ($row["id"] == $_POST["sel_project"]) {
            echo "<option value='".$row["id"]."' selected>".$row["name"]."</option>";
		} else {
            echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
		}
        //$project_name = $row["name"];
    }
    echo "</select>";
} else {
    echo "0 results";
}
?>
			<br/>
			<b>Select Standard:</b>
<?php
if (isset($_POST["sel_project"]) && $_POST["sel_project"] != '-') {

$sql = "SELECT * FROM `standard` WHERE `idProject` = ".$_POST["sel_project"];

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    echo "<select name='sel_standard' onchange='form.submit()'>";
    echo "<option value='-'>Please select standard ...</option>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        if ($row["id"] == $_POST["sel_standard"]) {
            echo "<option value='".$row["id"]."' selected>".$row["name"]."</option>";
		} else {
            echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
		}
        //$project_name = $row["name"];
    }
    echo "</select>";
} else {
    echo "0 results";
}
} else {
	echo "<b>no project selected, please select project first!</b>";
}
?>
			<br/>
			<b>Select Header Definition:</b>
<?php
if (isset($_POST["sel_standard"]) && $_POST["sel_standard"] != '-' && $_POST["sel_project"] != '-') {
    // are headers already defined?

 echo "<br/>";

    // type = 0: TC header
    $sql = "SELECT * FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 0 AND p.kind = 1 AND p.idStandard = ".$idStandard." ORDER BY ps.order ASC"; 
    $result = $mysqli->query($sql);
    //$rows_tc = $result->fetch_assoc();

    /*while($row = $result->fetch_assoc()) {
        echo $row['name']."<br/>";
    }*/

    $num_rows_tc = mysqli_num_rows($result);

    if ($num_rows_tc > 0) {
        echo "TC Header is already defined!";
    } else {
		
        if (isset($_POST["sel_tc_header"]) && $_POST["sel_tc_header"] == 'on') {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_tc_header' onchange='form.submit()' checked> TC Header";
		} else {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_tc_header' onchange='form.submit()' > TC Header";
		}

        $sql = "SELECT * FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 0 AND p.kind = 1 AND p.idStandard = ".$_POST["sel_standard"]." ORDER BY ps.order ASC"; 
        $result = $mysqli->query($sql);
        $rows_tc = $result->fetch_all();

    }

 echo "<br/>";
 
    // type = 1: TM header
    $sql = "SELECT * FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 1 AND p.kind = 1 AND p.idStandard = ".$idStandard." ORDER BY ps.order ASC";  
    $result = $mysqli->query($sql);
    //$row_tm = $result->fetch_all();

    /*while($row = $result->fetch_assoc()) {
        echo $row['name']."<br/>";
    }*/

    $num_rows_tm = mysqli_num_rows($result);

    if ($num_rows_tm > 0) {
        echo "TM Header is already defined!";
    } else {
        if (isset($_POST["sel_tm_header"]) && $_POST["sel_tm_header"] == 'on') {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_tm_header' onchange='form.submit()' checked> TM Header";
        } else {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_tm_header' onchange='form.submit()' > TM Header";
        }

        $sql = "SELECT * FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 1 AND p.kind = 1 AND p.idStandard = ".$_POST["sel_standard"]." ORDER BY ps.order ASC"; 
        $result = $mysqli->query($sql);
        $rows_tm = $result->fetch_all();

    }

    // kind = 1: for parameters which can only be used in TC/TM header definitions; CordetEditorDatabaseModel.pdf (PP-DF-COR-0004, Revision 1.0)
/*    $sql = "SELECT * FROM `parameter` WHERE `kind` = 1 AND `idStandard` = ".$idStandard; 
    $result = $mysqli->query($sql);
	
    while($row = $result->fetch_assoc()) {
        echo $row['name']."<br/>";
    }
	
    $num_rows = mysqli_num_rows($result);
    if ($num_rows > 0) {
        echo "TM/TC Headers are already defined!";
    } else {
        echo "<br/><input type='checkbox' name='sel_header' > TM/TC Header";
    }*/
} else {
    echo "<b>no standard selected, please select standard first!</b>";
}

?>
			<br/>
			<b>Select Service:</b>
<?php
if (isset($_POST["sel_standard"]) && $_POST["sel_standard"] != '-' && $_POST["sel_project"] != '-') {

$sql = "SELECT * FROM `service` WHERE `idStandard` = ".$_POST["sel_standard"]." ORDER BY `type` ASC";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if (isset($_POST["sel_service_type"])) { $sel_service_type  = $_POST["sel_service_type"]; } else { $sel_service_type=array(); };

if ($result->num_rows > 0) {
    echo "<br/><fieldset>";  
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        if (isset($_POST["sel_service_subtype"][$row["id"]])) { $sel_service_subtype[$row["id"]]  = $_POST["sel_service_subtype"][$row["id"]]; } else { $sel_service_subtype[$row["id"]]=array(); };
        if (in_array($row["id"], $sel_service_type)) {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_service_type[]' value='".$row["id"]."' onchange='form.submit()' checked> ".$row["name"]." (Type: ".$row["type"].")<br/>";



            $sel_service_subtypes[$row["id"]] = get_sub_types($mysqli, $_POST["sel_standard"], $row["type"], $row["id"], $sel_service_subtype[$row["id"]]);
            //echo "<input type='checkbox' name='sel_service_subtype[".$row["id"]."][]' value='Test' onchange='form.submit()' checked>(".$row["type"].") ".$row["name"]."</input><br/>";



        } else {
            echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='sel_service_type[]' value='".$row["id"]."' onchange='form.submit()'> ".$row["name"]." (Type: ".$row["type"].")<br/>";
        }
        //$project_name = $row["name"];
    }
    echo "</fieldset>";
} else {
    echo "0 results<br/>";
}
    echo "<br/>";
    echo "<input type='submit' name='import' value='Import now'>";
    echo "<br/>";
    echo "<br/>";


if (isset($_POST["sel_tc_header"]) && $_POST["sel_tc_header"] == 'on') {
    echo "Import TC Header<br/>";

    foreach($rows_tc as $row_tc) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row_tc[5]."<br/>";
    }

    /*foreach($rows_tc as $x => $x_value) {
      echo "Key=" . $x . ", Value=" . $x_value[5]."<br/>";
    }*/
}

if (isset($_POST["sel_tm_header"]) && $_POST["sel_tm_header"] == 'on') {
    echo "Import TM Header<br/>";
    foreach($rows_tm as $row_tm) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row_tm[5]."<br/>";
    }
}

if(!empty($sel_service_type)) {
    foreach($sel_service_type as $check) {
            echo $check." | <br/>";
            if(!empty($sel_service_subtypes[$check])) {
                foreach($sel_service_subtypes[$check] as $subtypes) {
                   echo $subtypes." | ";
                }
            }
            echo "<br/>";
            if(!empty($sel_service_subtype[$check])) {
                foreach($sel_service_subtype[$check] as $subtype) {
                   echo $subtype." | ";
                }
            }
            echo "<br/>";
    }
}
    echo "<br/>";

} else {
	echo "<b>no standard selected, please select standard first!</b>";
}

/**
 * #############################
 * ### Import selected items ###
 * #############################
 */
if (isset($_POST["import"])) {
    echo "Submit Button clicked...<br/>";

    if (isset($_POST["sel_tc_header"]) && $_POST["sel_tc_header"] == 'on') {
    // Import TC Header
    $sql = "SELECT p.id as pid, p.desc as pdesc, p.value as pvalue, p.setting as psetting, p.role as prole, ps.id as psid, p.*, ps.* FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 0 AND p.kind = 1 AND p.idStandard = ".$_POST["sel_standard"]." ORDER BY ps.order ASC";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<b>TC parameter:</b> (".$row['pid'].",".$row['idStandard'].",".$row['idType'].",".$row['kind'].",".$row['domain'].",".$row['name'].",".$row['shortDesc'].",".$row['pdesc'].",".$row['pvalue'].",".$row['size'].",".$row['unit'].",".$row['multiplicity'].",".$row['psetting'].",".$row['prole'].")<br/>";

            echo "<b>TC parametersequence:</b> (".$row['psid'].",".$row['idStandard'].",".$row['idParameter'].",".$row['idPacket'].",".$row['type'].",".$row['role'].",".$row['order'].",".$row['group'].",".$row['repetition'].",".$row['value'].",".$row['desc'].",".$row['setting'].")<br/>";

            $sql_type = "SELECT * FROM `type` WHERE id = ".$row['idType'];
            $result_type = $mysqli->query($sql_type);
            $row_type = $result_type->fetch_assoc();
            if (empty($row_type['idStandard'])) {
                echo "<font color=red><b>TC type:</b> (".$row_type['id'].")</font><br/><br/>";
            } else {
                // check if type already inserted: $idStandard / $row['idStandard']
                $sql_type_check = "SELECT id FROM `type` WHERE `idStandard` = '".$idStandard."' AND `domain` = '".$row_type['domain']."' AND `name` = '".$row_type['name']."' AND `nativeType` = '".$row_type['nativeType']."' AND `desc` = '".$row_type['desc']."' AND `size` = '".$row_type['size']."' AND `value` = '".$row_type['value']."' AND `setting` = '".$row_type['setting']."' AND `schema` = '".$row_type['schema']."'";
                $result_type_check = $mysqli->query($sql_type_check);
                $num_rows_type_check = $result_type_check->num_rows;
                echo "<font color=blue>=> ".$num_rows_type_check."</font><br/>";
                if ($num_rows_type_check == 0) {
                    echo "<font color=blue><b>TC type:</b></font> (".$row_type['id'].",".$row_type['idStandard'].",".$row_type['domain'].",".$row_type['name'].",".$row_type['nativeType'].",".$row_type['desc'].",".$row_type['size'].",".$row_type['value'].",".$row_type['setting'].",".$row_type['schema'].")<br/>";
                    
                    // INSERT 'type'
                    $sql_insert_type = "INSERT INTO `type` ".
                    "(`idStandard`, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, `setting`, `schema`) ".
                    "VALUES ".
                    "(".$idStandard.", '".$row_type['domain']."', '".$row_type['name']."', '".$row_type['nativeType']."', '".$row_type['desc']."', ".$row_type['size'].", '".$row_type['value']."', '".$row_type['setting']."', '".$row_type['schema']."')";
                    echo "<font color=red>".$sql_insert_type."</font><br/>";
                    $result_insert_type = $mysqli->query($sql_insert_type);
                    // get type id
                    $idType = $mysqli->insert_id;
                    echo "<font color=red>".$idType."</font><br/>";
                    
               } else {
                   $row_type_check = $result_type_check->fetch_assoc();
                   echo "<font color=blue>TC type exists already with id = ".$row_type_check['id'].".</font><br/>";
                   // get existing type id
                   $idType = $row_type_check['id'];
                   echo "<font color=red>".$idType."</font><br/>";
               }
            }

            // INSERT `parameter`
            $desc_corr = $row['pdesc'];
            if ($desc_corr == "null") {
                $desc_corr = "";
            }
            $size_corr = $row['size'];
            if ($size_corr == "") {
                $size_corr = "NULL";
            }
            $sql_insert_parameter = "INSERT INTO `parameter` ".
            "(`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`, `setting`, `role`) ".
            "VALUES ".
            "(".$idStandard.", ".$idType.", ".$row['kind'].", '".$row['domain']."', '".$row['name']."', '".$row['shortDesc']."', '".$desc_corr."', '".$row['pvalue']."', ".$size_corr.", '".$row['unit']."', '".$row['multiplicity']."', '".$row['psetting']."', ".$row['prole'].")";
            echo "<font color=red>".$sql_insert_parameter."</font><br/>";
            $result_insert_parameter = $mysqli->query($sql_insert_parameter);
            // get parameter id
            $idParameter = $mysqli->insert_id;
            echo "<font color=red>".$idParameter."</font><br/>";

            // INSERT `parametersequence`
            $sql_insert_parametersequence = "INSERT INTO `parametersequence` ".
            "(`idStandard`, `idParameter`, `type`, `role`, `order`, `group`, `repetition`, `value`, `desc`, `setting`) ".
            "VALUES ".
            "(".$idStandard.", ".$idParameter.", ".$row['type'].", ".$row['role'].", ".$row['order'].", ".$row['group'].", ".$row['repetition'].", '".$row['value']."', '".$row['desc']."', '".$row['setting']."')";
            echo "<font color=red>".$sql_insert_parametersequence."</font><br/>";
            $result_insert_parametersequence = $mysqli->query($sql_insert_parametersequence);

            echo "<br/>";

        }
    } else {
        echo "<b>NO TC HEADER FOUND</b><br/><br/>";
    }
    }

    if (isset($_POST["sel_tm_header"]) && $_POST["sel_tm_header"] == 'on') {
    // Import TM Header
    $sql = "SELECT p.id as pid, p.desc as pdesc, p.value as pvalue, p.setting as psetting, p.role as prole, ps.id as psid, p.*, ps.* FROM `parameter` AS p, `parametersequence` AS ps WHERE p.id = ps.idParameter AND ps.type = 1 AND p.kind = 1 AND p.idStandard = ".$_POST["sel_standard"]." ORDER BY ps.order ASC";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<b>TM parameter:</b> (".$row['pid'].",".$row['idStandard'].",".$row['idType'].",".$row['kind'].",".$row['domain'].",".$row['name'].",".$row['shortDesc'].",".$row['pdesc'].",".$row['pvalue'].",".$row['size'].",".$row['unit'].",".$row['multiplicity'].",".$row['psetting'].",".$row['prole'].")<br/>";
            
            echo "<b>TM parametersequence:</b> (".$row['psid'].",".$row['idStandard'].",".$row['idParameter'].",".$row['idPacket'].",".$row['type'].",".$row['role'].",".$row['order'].",".$row['group'].",".$row['repetition'].",".$row['value'].",".$row['desc'].",".$row['setting'].")<br/>";

            $sql_type = "SELECT * FROM `type` WHERE id = ".$row['idType'];
            $result_type = $mysqli->query($sql_type);
            $row_type = $result_type->fetch_assoc();
            if (empty($row_type['idStandard'])) {
                echo "<font color=red><b>TM type:</b> (".$row_type['id'].")</font><br/><br/>";
            } else {
                // check if type already inserted: $idStandard / $row['idStandard']
                $sql_type_check = "SELECT id FROM `type` WHERE `idStandard` = '".$idStandard."' AND `domain` = '".$row_type['domain']."' AND `name` = '".$row_type['name']."' AND `nativeType` = '".$row_type['nativeType']."' AND `desc` = '".$row_type['desc']."' AND `size` = '".$row_type['size']."' AND `value` = '".$row_type['value']."' AND `setting` = '".$row_type['setting']."' AND `schema` = '".$row_type['schema']."'";
                $result_type_check = $mysqli->query($sql_type_check);
                $num_rows_type_check = $result_type_check->num_rows;
                echo "<font color=blue>=> ".$num_rows_type_check."</font><br/>";
                if ($num_rows_type_check == 0) {
                    echo "<font color=blue><b>TM type:</b></font> (".$row_type['id'].",".$row_type['idStandard'].",".$row_type['domain'].",".$row_type['name'].",".$row_type['nativeType'].",".$row_type['desc'].",".$row_type['size'].",".$row_type['value'].",".$row_type['setting'].",".$row_type['schema'].")<br/>";
                    
                    // INSERT 'type'
                    $sql_insert_type = "INSERT INTO `type` ".
                    "(`idStandard`, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, `setting`, `schema`) ".
                    "VALUES ".
                    "(".$idStandard.", '".$row_type['domain']."', '".$row_type['name']."', '".$row_type['nativeType']."', '".$row_type['desc']."', ".$row_type['size'].", '".$row_type['value']."', '".$row_type['setting']."', '".$row_type['schema']."')";
                    echo "<font color=red>".$sql_insert_type."</font><br/>";
                    $result_insert_type = $mysqli->query($sql_insert_type);
                    // get type id
                    $idType = $mysqli->insert_id;
                    //$idType = 3;
                    echo "<font color=red>".$idType."</font><br/>";
                    
               } else {
                   $row_type_check = $result_type_check->fetch_assoc();
                   echo "<font color=blue>TM type exists already with id = ".$row_type_check['id'].".</font><br/>";
                   // get existing type id
                   $idType = $row_type_check['id'];
                   echo "<font color=red>".$idType."</font><br/>";
               }
            }

            // INSERT `parameter`
            $desc_corr = $row['pdesc'];
            if ($desc_corr == "null") {
                $desc_corr = "";
            }
            $size_corr = $row['size'];
            if ($size_corr == "") {
                $size_corr = "NULL";
            }
            $sql_insert_parameter = "INSERT INTO `parameter` ".
            "(`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`, `setting`, `role`) ".
            "VALUES ".
            "(".$idStandard.", ".$idType.", ".$row['kind'].", '".$row['domain']."', '".$row['name']."', '".$row['shortDesc']."', '".$desc_corr."', '".$row['pvalue']."', ".$size_corr.", '".$row['unit']."', '".$row['multiplicity']."', '".$row['psetting']."', ".$row['prole'].")";
            echo "<font color=red>".$sql_insert_parameter."</font><br/>";
            $result_insert_parameter = $mysqli->query($sql_insert_parameter);
            // get parameter id
            $idParameter = $mysqli->insert_id;
            //$idParameter = 123456;
            echo "<font color=red>".$idParameter."</font><br/>";

            // INSERT `parametersequence`
            $sql_insert_parametersequence = "INSERT INTO `parametersequence` ".
            "(`idStandard`, `idParameter`, `type`, `role`, `order`, `group`, `repetition`, `value`, `desc`, `setting`) ".
            "VALUES ".
            "(".$idStandard.", ".$idParameter.", ".$row['type'].", ".$row['role'].", ".$row['order'].", ".$row['group'].", ".$row['repetition'].", '".$row['value']."', '".$row['desc']."', '".$row['setting']."')";
            echo "<font color=red>".$sql_insert_parametersequence."</font><br/>";
            $result_insert_parametersequence = $mysqli->query($sql_insert_parametersequence);

            echo "<br/>";

        }
    } else {
        echo "<b>NO TM HEADER FOUND</b><br/><br/>";
    }
    }
    
    if(!empty($sel_service_type)) {
    // Import Services
        foreach($sel_service_type as $check) {
            echo "Service: id = ".$check." <br/>";
            
            $sql_serv = "SELECT * FROM `service` WHERE `idStandard` = '".$_POST["sel_standard"]."' AND `id` = ".$check;
            $result_serv = $mysqli->query($sql_serv);
            $num_rows_serv = $result_serv->num_rows;
            if ($num_rows_serv == 1) {
                $row_serv = $result_serv->fetch_assoc();
                // check if service with same type already exists: $idStandard / $row_serv['idStandard']
                $sql_serv_check = "SELECT * FROM `service` WHERE `idStandard` = '".$idStandard."' AND `type` = ".$row_serv['type'];
                $result_serv_check = $mysqli->query($sql_serv_check);
                $num_rows_serv_check = $result_serv_check->num_rows;
                echo "<font color=blue>=> ".$num_rows_serv_check."</font><br/>";
                if ($num_rows_serv_check == 0) {
                    echo "<font color=blue><b>Service</b></font> (".$row_serv['id'].",".$row_serv['idStandard'].",".$row_serv['name'].",".$row_serv['desc'].",".$row_serv['type'].",".$row_serv['setting'].")<br/>";
                    
                    // INSERT `service`
                    $sql_insert_service = "INSERT INTO `service` ".
                    "(`idStandard`, `name`, `desc` , `type`) ".
                    "VALUES ".
                    "(".$idStandard.", '".$row_serv['name']."', '".$row_serv['desc']."', ".$row_serv['type'].")";
                    echo "<font color=red>".$sql_insert_service."</font><br/>";
                    $result_insert_service = $mysqli->query($sql_insert_service);
                    // get service id
                    $idService = $mysqli->insert_id;
                    //$idService = 12;
                    echo "<font color=red>".$idService."</font><br/>";
                    
                } else {
                   $row_serv_check = $result_serv_check->fetch_assoc();
                   echo "<font color=blue>Service exists already with id = ".$row_serv_check['id'].".</font><br/>";
                   // get existing service id
                   $idService = $row_serv_check['id'];
                   echo "<font color=red>".$idService."</font><br/>";
                }
            } else {
                echo "<b>NO SERVICE FOUND (id = ".$check.")</b><br/><br/>";
            }
            
            echo "<br/>"; 
            
            // Sub-Services
            if(!empty($sel_service_subtypes[$check])) {
                foreach($sel_service_subtypes[$check] as $subtypes) {
                   echo $subtypes." | ";
                }
            }
            echo "<br/>";
            if(!empty($sel_service_subtype[$check])) {
            // Import Sub-Services
                foreach($sel_service_subtype[$check] as $subtype) {
                   echo "Sub-Service: id = ".$subtype." <br/>";
                   
                   $sql_subserv = "SELECT * FROM `packet` WHERE `idStandard` = ".$_POST["sel_standard"]." AND `id`= ".$subtype;
                   $result_subserv = $mysqli->query($sql_subserv);
                   $num_rows_subserv = mysqli_num_rows($result_subserv);
                   if ($num_rows_subserv == 1) {
                        $row_subserv = $result_subserv->fetch_assoc();
                   
                        // check if sub-service with same sub-type already exists: $idStandard / $row_serv['idStandard']
                        $sql_subserv_check = "SELECT * FROM `packet` WHERE `idStandard` = '".$idStandard."' AND `type` = ".$row_serv['type']." AND `subtype`= ".$row_subserv['subtype'];
                        $result_subserv_check = $mysqli->query($sql_subserv_check);
                        $num_rows_subserv_check = $result_subserv_check->num_rows;
                        echo "<font color=blue>=> ".$num_rows_subserv_check."</font><br/>";
                        if ($num_rows_subserv_check == 0) {
                            echo "<font color=blue><b>Sub-Service</b></font> (".$row_subserv['id'].",".$row_subserv['idStandard'].",".$row_subserv['type'].",".$row_subserv['name'].",".$row_subserv['desc'].",".$row_subserv['setting'].")<br/><br/>";
                        
                            // INSERT `packet` (sub-service)
                            $sql_insert_packet = "INSERT INTO `packet` ".
                            "(`idStandard`, `kind`, `type`, `subtype`, `discriminant`, `domain`, `name`, `shortDesc`, `desc`, `descParam`, `descDest`, `code`) ".
                            "VALUES ".
                            "(".$idStandard.", ".$row_subserv['kind'].", ".$row_subserv['type'].", ".$row_subserv['subtype'].", '".$row_subserv['discriminant']."', '".$row_subserv['domain']."', '".$row_subserv['name']."', '".$row_subserv['shortDesc']."', '".$row_subserv['desc']."', '".$row_subserv['descParam']."', '".$row_subserv['descDest']."', '".$row_subserv['code']."')";
                            echo "<font color=red>".$sql_insert_packet."</font><br/>";
                            $result_insert_packet = $mysqli->query($sql_insert_packet);
                            // get packet id (sub-service)
                            $idSubService = $mysqli->insert_id;
                            //$idSubService = 123;
                            echo "<font color=red>".$idSubService."</font><br/>";
                        
                        } else {
                            $row_subserv_check = $result_subserv_check->fetch_assoc();
                            echo "<font color=blue>Sub-servivce exists already with id = ".$row_subserv_check['id'].".</font><br/>";
                            // get existing type id
                            $idSubService = $row_subserv_check['id'];
                            echo "<font color=red>".$idSubService."</font><br/>";
                        }
                        
                        echo "<br/>"; 
                        
                        // get packet parameters
                            $sql_pckt_param = "SELECT * FROM `parametersequence` WHERE `idPacket` = ".$row_subserv['id'];
                            $result_pckt_param = $mysqli->query($sql_pckt_param);
                            $num_rows_pckt_param = mysqli_num_rows($result_pckt_param);
                            if ($num_rows_pckt_param > 0) {
                                while ($row_pckt_param = $result_pckt_param->fetch_assoc()) {

                                    // check if parametersequence already exists IS NOT DONE
                                    echo "<b>Parametersequence:</b> (".$row_pckt_param['id'].", ".$row_pckt_param['idParameter'].", ".$row_pckt_param['desc'].")<br/>";
                                
                                    $sql_param = "SELECT * FROM `parameter` WHERE id = ".$row_pckt_param['idParameter'];
                                    $result_param = $mysqli->query($sql_param);
                                    $num_rows_param = mysqli_num_rows($result_param);
                                    if ($num_rows_param == 1) {
                                        $row_param = $result_param->fetch_assoc();
                                        
                                        // check if parameter already exists:: $idStandard / $row_serv['idStandard']
                                        //$sql_param_check = "SELECT * FROM `parameter` WHERE `idStandard` = ".$row_serv['idStandard']." AND `kind` = ".$row_param['kind']." AND `domain` = '".$row_param['domain']."' AND `name` = '".$row_param['name']."' AND `shortDesc` = '".$row_param['shortDesc']."' AND `desc` = '".$row_param['desc']."' AND `value` = '".$row_param['value']."' AND `size` = '".$row_param['size']."' AND `unit` = '".$row_param['unit']."' AND `multiplicity` = '".$row_param['multiplicity']."' AND `setting` = '".$row_param['setting']."' AND `role` = '".$row_param['role']."'";
                                        $sql_param_check = "SELECT * FROM `parameter` WHERE `idStandard` = ".$idStandard." AND `kind` = ".$row_param['kind']." AND `domain` = '".$row_param['domain']."' AND `name` = '".$row_param['name']."' AND `shortDesc` = '".$row_param['shortDesc']."' AND `desc` = '".$row_param['desc']."' AND `value` = '".$row_param['value']."' AND `unit` = '".$row_param['unit']."' AND `setting` = '".$row_param['setting']."'";
                                        echo $sql_param_check."<br/>";
                                        $result_param_check = $mysqli->query($sql_param_check);
                                        $num_rows_param_check = $result_param_check->num_rows;
                                        echo "<font color=blue>=> ".$num_rows_param_check."</font><br/>";
                                        if ($num_rows_param_check == 0) {
                                            echo "<b>Parameter:</b> (".$row_param['id'].", ".$row_param['name'].")<br/>";
                                            
                                            // check type !!!
                                            
                                            
            $sql_type = "SELECT * FROM `type` WHERE id = ".$row_param['idType'];
            $result_type = $mysqli->query($sql_type);
            $row_type = $result_type->fetch_assoc();
            if (empty($row_type['idStandard'])) {
                echo "<font color=red><b>TM type:</b> (".$row_type['id'].")</font><br/><br/>";
            } else {
                // check if type already inserted: $idStandard / $row['idStandard']
                $sql_type_check = "SELECT id FROM `type` WHERE `idStandard` = '".$idStandard."' AND `domain` = '".$row_type['domain']."' AND `name` = '".$row_type['name']."' AND `nativeType` = '".$row_type['nativeType']."' AND `desc` = '".$row_type['desc']."' AND `size` = '".$row_type['size']."' AND `value` = '".$row_type['value']."' AND `setting` = '".$row_type['setting']."' AND `schema` = '".$row_type['schema']."'";
                $result_type_check = $mysqli->query($sql_type_check);
                $num_rows_type_check = $result_type_check->num_rows;
                echo "<font color=blue>=> ".$num_rows_type_check."</font><br/>";
                if ($num_rows_type_check == 0) {
                    echo "<font color=blue><b>TM type:</b></font> (".$row_type['id'].",".$row_type['idStandard'].",".$row_type['domain'].",".$row_type['name'].",".$row_type['nativeType'].",".$row_type['desc'].",".$row_type['size'].",".$row_type['value'].",".$row_type['setting'].",".$row_type['schema'].")<br/>";
                    
                    // INSERT 'type'
                    $sql_insert_type = "INSERT INTO `type` ".
                    "(`idStandard`, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, `setting`, `schema`) ".
                    "VALUES ".
                    "(".$idStandard.", '".$row_type['domain']."', '".$row_type['name']."', '".$row_type['nativeType']."', '".$row_type['desc']."', ".$row_type['size'].", '".$row_type['value']."', '".$row_type['setting']."', '".$row_type['schema']."')";
                    echo "<font color=red>".$sql_insert_type."</font><br/>";
                    $result_insert_type = $mysqli->query($sql_insert_type);
                    // get type id
                    $idType = $mysqli->insert_id;
                    //$idType = 3;
                    echo "<font color=red>".$idType."</font><br/>";
                    
               } else {
                   $row_type_check = $result_type_check->fetch_assoc();
                   echo "<font color=blue>TM type exists already with id = ".$row_type_check['id'].".</font><br/>";
                   // get existing type id
                   $idType = $row_type_check['id'];
                   echo "<font color=red>".$idType."</font><br/>";
               }
            }
                                            
            // INSERT `parameter`
            $desc_corr = $row_param['desc'];
            if ($desc_corr == "null") {
                $desc_corr = "";
            }
            $size_corr = $row_param['size'];
            if ($size_corr == "") {
                $size_corr = "NULL";
            }
            $sql_insert_parameter = "INSERT INTO `parameter` ".
            "(`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`, `setting`, `role`) ".
            "VALUES ".
            "(".$idStandard.", ".$idType.", ".$row_param['kind'].", '".$row_param['domain']."', '".$row_param['name']."', '".$row_param['shortDesc']."', '".$desc_corr."', '".$row_param['value']."', ".$size_corr.", '".$row_param['unit']."', '".$row_param['multiplicity']."', '".$row_param['setting']."', ".$row_param['role'].")";
            echo "<font color=red>".$sql_insert_parameter."</font><br/>";
            $result_insert_parameter = $mysqli->query($sql_insert_parameter);
            // get parameter id
            $idParameter = $mysqli->insert_id;
            //$idParameter = 123456;
            echo "<font color=red>".$idParameter."</font><br/>";

            // INSERT `parametersequence`
            $sql_insert_parametersequence = "INSERT INTO `parametersequence` ".
            "(`idStandard`, `idParameter`, `idPacket`, `type`, `role`, `order`, `group`, `repetition`, `value`, `desc`, `setting`) ".
            "VALUES ".
            "(".$idStandard.", ".$idParameter.", ".$idSubService.", ".$row_pckt_param['type'].", ".$row_pckt_param['role'].", ".$row_pckt_param['order'].", ".$row_pckt_param['group'].", ".$row_pckt_param['repetition'].", '".$row_pckt_param['value']."', '".$row_pckt_param['desc']."', '".$row_pckt_param['setting']."')";
            echo "<font color=red>".$sql_insert_parametersequence."</font><br/>";
            $result_insert_parametersequence = $mysqli->query($sql_insert_parametersequence);
                                            
                                            
                                            
                                            
                                            
                                        } else {
                                            echo "<font color=red><b>Parameter exists already!</b></font><br/>";
                                        }
                                        
                                        echo "<br/>";
                                    
                                    }
                                
                                }
                            } else {
                                echo "No parameters found!<br/><br/>";
                            }
                        
                        
                        
                        
                        
                   }
                   
                }
            }
            echo "<br/>";
        }
    }

}

?>


</form>
		</div>

				<div class="topcorner_left">
					<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
					<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
					<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>