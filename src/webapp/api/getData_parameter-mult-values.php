<?php 
/*
$name = $_COOKIE['name'];
$mult = $_COOKIE['mult'.$name];
$value = $_COOKIE['value'.$name];
*/
require 'db_config.php';

if (isset($_GET["idStandard"])) { $idStandard  = $_GET["idStandard"]; } else { $idStandard=0; };

$post = $_POST;

if (isset($_POST['mult'])) { $mult = $_POST['mult']; } else { $mult = 1; }
if (isset($_POST['value'])) { $value = $_POST['value']; } else { $value = 0; }

//echo "<br/>";
//echo "multiplicity = $mult<br/>";
//echo "value = $value<br/>";

//$value = "{DpIdDpuMode,DpIdDpuUnit,DpIdRseShutSts,DpIdPsuSts,DpIdAdcP3V9,DpIdAdcP3V3,DpIdAdcP3V3LVDS,DpIdAdcP2V5,DpIdAdcP1V8,DpIdAdcP1V2,DpIdAdcRef,DpIdAdcIFeeAna,DpIdAdcIFeeDig,DpIdAdcIDpu,DpIdAdcIRse,DpIdAdcIHeater,DpIdAdcTemp1,DpIdAdcTempCcd,DpIdAdcTempFee,DpIdAdcPsuTemp,DpIdErrCnt,DpIdErrLastEventId,DpIdVersionNumber,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0}";

//$value = "{DpIdDpuMode,DpIdDpuUnit,DpIdRseShutSts,DpIdPsuSts,VarItemTemp, 0,0x12,0,C0,0}";

//$value = "{0.1,0.2}";

//$value = "{0,1,2,,,,,,,}";

$val_array = [];

$klammer1 = explode("{", $value);
if (count($klammer1)>1) {
    $klammer2 = explode("}", $klammer1[1]);
    if (count($klammer2)>1) {
        $value_list = $klammer2[0];
        //echo "value_list = ".$value_list."<br/>";
        //$values = explode(",", $value_list);
        $values = array_map('trim', explode(',', $value_list));
        $val_count = count($values);
        //echo "val_count = ".$val_count."<br/>";
        foreach ($values as $v) {
            $val_array[] = $v;
        }
    }
} else {
    //echo "value = ".$value."<br/>";
    $val_array[] = $value;
    $val_count = 1;
}
//print_r($val_array);


$sql = "SELECT * FROM `parameter` WHERE `idStandard` = ".$idStandard." AND kind IN (3, 4, 5, 6) ORDER BY `name` ASC";
$result = $mysqli->query($sql);
$num_rows_p = mysqli_num_rows($result);
if ($result->num_rows > 0) {
    $params[] = "-- Select Param --";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $params[] = "DpId".$row["name"];
    }
}

$sql = "SELECT * FROM `constants` WHERE `idStandard` = ".$idStandard." ORDER BY `name` ASC";
$result = $mysqli->query($sql);
$num_rows_c = mysqli_num_rows($result);
if ($result->num_rows > 0) {
    $consts[] = "-- Select Const --";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $consts[] = $row["name"];
    }
}
//$num_rows_c = 1;
//$consts[] = "-- Select Const --";
//$consts[] = "C0";

if ($val_count < $mult) {
    echo "<b><font color=red>There are too few default values given!</font></b><br/>";
} else if ($val_count > $mult) {
    echo "<b><font color=red>There are too many default values given!</font></b><br/>";
    
}

for($i=0; $i<$mult; $i++) {
    $color = "white";
    if ($i >= $val_count) {
        $val_array[$i] = "";
        $color = "#F75D59";  // bean red
    } else {
    
        // check if first character is not numeric
        $not_found = false;
        if (strlen($val_array[$i]) > 0) {
            if (!is_numeric($val_array[$i][0])) {
                $not_found = true;
                if ($num_rows_p > 0) {
                    if (in_array($val_array[$i], $params)) {
                        $not_found = false;
                    } 
                }
                if ($num_rows_c > 0) {
                    if (in_array($val_array[$i], $consts)) {
                        $not_found = false;
                    }
                }
            }
        } else {
            $color = "lightgray";
            echo "<b><font color=red>There is a empty string value given!</font></b><br/>";
        }
        
        if ($num_rows_p > 0) {
        if (in_array($val_array[$i], $params)) {
            $color = "#DBF9DB";  // light rose green
        }
        }
        if ($num_rows_c > 0) {
        if (in_array($val_array[$i], $consts)) {
            $color = "LightYellow";
        }
        }
        if ($not_found) {
            $color = "#FFE5CC";  // lightorange
        }
        
        // check if hex value
        //if (preg_match('/^(?:0x)?[a-f0-9]{1,}$/i', $val_array[$i])) {
        if (preg_match('/^(0x)[a-f0-9]{1,}$/i', $val_array[$i])) {
            $color = "lightblue";
        }
    
    }
    
    echo '<div class="form-control" style="float:left;width:5%;border:0;background-color:lightgray;padding-left:2px;">'.$i.'</div>';
    echo '<input id="values_'.$i.'" type="text" name="values[]" class="form-control" style="width:45%;float:left;background-color:'.$color.';" value="'.$val_array[$i].'" onfocus="txt_onfocus(this)" data-error="Please enter value." required />';
    if ($num_rows_p > 0) {
    echo '<select id="sel_params_'.$i.'" name="params[$i]" class="form-control" style="width:25%;float:right;" onchange="myFunctionP(event, '.$i.')" data-error="Please enter parameter.">';
    foreach ($params as $p) {
        //if (in_array($val_array[$i], $params)) {
        if ($val_array[$i] == $p) {
            echo "<option value='$p' selected>$p</option>";
        } else {
            echo "<option value='$p'>$p</option>";
        }
    }
    echo '</select>';
    } else {
        echo '<span style="height:34px;width:25%;float:right;">&nbsp;</span>';
    }
    if ($num_rows_c > 0) {
    echo '<select id="sel_consts_'.$i.'" name="consts[$i]" class="form-control" style="width:25%;float:right;" onchange="myFunctionC(event, '.$i.')" data-error="Please enter constant.">';
    foreach ($consts as $c) {
        //if (in_array($val_array[$i], $consts)) {
        if ($val_array[$i] == $c) {
            echo "<option value='$c' selected>$c</option>";
                    } else {
            echo "<option value='$c'>$c</option>";
        }
    }
    echo '</select>';
    } else if ($num_rows_p != 0) {
        echo '<span style="height:34px;width:25%;float:right;">&nbsp;</span>';
    }
    //echo '<br/>';
}
?>