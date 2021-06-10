<!DOCTYPE html>
<html>

<?php

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
    echo "0 results";
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
    echo "0 results";
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
        echo "0 results";
    }

    return $array;

}


?>

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
	<script type="text/javascript">
		var url = "http://localhost/dbeditor/";
	</script>
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

    while($row = $result->fetch_assoc()) {
        echo $row['name']."<br/>";
    }

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

    while($row = $result->fetch_assoc()) {
        echo $row['name']."<br/>";
    }

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
    echo "<input type='submit' value='Submit now'>";
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
?>


</form>
		</div>

				<div class="topcorner_left">
					<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
					<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
					<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
					<br/><br/>
					<a class="a_btn" href="open_standard.php?idProject=<?php echo $idProject; ?>&idStandard=<?php echo $idStandard; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>