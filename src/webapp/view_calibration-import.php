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

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (isset($_POST["idProject"])) { $idProject  = $_POST["idProject"]; } else { $idProject=0; };
if (isset($_POST["idApplication"])) { $idApplication  = $_POST["idApplication"]; } else { $idApplication=0; };
if (isset($_GET["csvDelimiter"])) { $csvDelimiter  = $_GET["csvDelimiter"]; } else { $csvDelimiter=" "; };
$project_name = "";
$application_name = "";
$application_desc = "";

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

$sql = "SELECT * FROM `application` WHERE `id`=".$idApplication;

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        $application_name = $row["name"];
        $application_desc = $row["desc"];
    }
} else {
    //echo "0 results";
}

/*
$sql = "SELECT * FROM `standard` WHERE `id`=".$idStandard;

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
*/

/*
// get all acronyms in database  
$acrNames[] = null;
$sqlTotal = 
  "SELECT ".
  "* ".
  "FROM ".
  "`acronym` ";
  
$result = $mysqli->query($sqlTotal);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "found: ".$row["name"]."<br/>";
        $acrNames[] = $row["name"]."|".$row["shortDesc"];
    }
} else {
    //echo "0 results";
}*/

// Check if image file is a actual image or fake image
if(isset($_POST["importCalList"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    //echo "File is an image - " . $check["mime"] . ".<br/>";
    //echo $target_file."<br/>";
    $uploadOk = 0;
    $messageDpImport = $target_file;
  } else {
    //echo "File is not an image.<br/>";
    $uploadOk = 1;
    $messageDpImport = "file detected.<br/>";
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  //echo "Sorry, file already exists.<br/>";
  //$uploadOk = 0;
  //echo "File already exists, and will be overwritten.<br/>";
  $uploadOk = 1;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "csv" && $imageFileType != "dat") {
  //echo "Sorry, only JPG, JPEG, PNG, GIF & CSV files are allowed.<br/>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  //echo "Sorry, your file was not uploaded.<br/>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    //echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.<br/>";
  } else {
    echo "Sorry, there was an error uploading your file.<br/>";
  }
}

function linkedToProject($mysqli, $idProject, $data) {
    $sql = "SELECT id FROM `acronym` WHERE `name` = '".$data[0]."' AND `shortDesc` = '".$data[1]."'";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    $row = $result->fetch_assoc();
    //echo "num_rows: ".$num_rows." | ID: ".$row['id'];
    if ($num_rows==1) {
        $sql_link = "SELECT * FROM `projectacronym` WHERE `idProject` = ".$idProject." AND `idAcronym` = ".$row['id'];
        $result_link = $mysqli->query($sql_link);
        $num_rows_link = mysqli_num_rows($result_link);
        if ($num_rows_link==1) {
            return true;
        }
    }
    return false;
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
	<title>CORDET Editor - Calibration Import</title>
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
	<script type="text/javascript" src="js/item-ajax_view-acronym-import.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
					<h4>Project <?php echo $project_name;?> - Application <?php echo $application_name;?></h4>
		            <h2>Calibration Import</h2>
					<!--<b>Delimiter:</b> <?php echo $csvDelimiter;?><br/><br/>-->
		        </div>
		        <!--<div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>-->
		    </div>
		</div>

<?php

// read CSV file
if($imageFileType == "dat" OR $imageFileType == "csv") {
  //echo "Read CSV file...<br/>";
  $rowNmb = 0;
  $exit = false;
  if ($_POST["idAction"]=="1") {  // Add new calibration
  if ($_POST["idCalCurve"]=="1") {  // Numerical Calibration Curve
      //$exit = true;
      echo "<h3>Numerical Calibration Curve</h3>";
      echo "<b>Name:</b> ".$_POST["name"]."<br/>";
      echo "<b>Short Description:</b> ".$_POST["shortDesc"]."<br/>";
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Polynomial Calibration Curve not supported!</font></b><br/>";
      echo "<b>Engineering Format:</b> ".$_POST["engFmt"]."<br/>";
      echo "<b>Raw Format:</b> ".$_POST["rawFmt"]."<br/>";
      echo "<b>Radix:</b> ".$_POST["radix"]."<br/>";
      echo "<b>Unit:</b> ".$_POST["unit"]."<br/>";
      echo "<b>N Curve:</b> ".$_POST["ncurve"]." (will be checked automatically)<br/>";
      echo "<b>Interpolation:</b> ".$_POST["inter"]."<br/>";
      $json0 = "{\"engfmt\":\"R\",\"rawfmt\":\"U\",\"radix\":\"D\",\"unit\":\"\",";
      $json2 = "\"values\":[";
      $json3 = "";
  } else if ($_POST["idCalCurve"]=="2") {  // Polynomial Calibration Curve
      //$exit = true;
      echo "<h3>Polynomial Calibration Curve</h3>";
      echo "<b>Name:</b> ".$_POST["name"]."<br/>";
      echo "<b>Short Description:</b> ".$_POST["shortDesc"]."<br/>";
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Polynomial Calibration Curve not supported!</font></b><br/>";
      $json0 = "{";
      $json1 = "";
      $json2 = "";
      $json3 = "";
  } else if ($_POST["idCalCurve"]=="3") {  // Logarithmical Calibration Curve
      //$exit = true;
      echo "<h3>Logarithmical Calibration Curve</h3>";
      echo "<b>Name:</b> ".$_POST["name"]."<br/>";
      echo "<b>Short Description:</b> ".$_POST["shortDesc"]."<br/>";
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Logarithmical Calibration Curve not supported!</font></b><br/>";
      $json0 = "{";
      $json1 = "";
      $json2 = "";
      $json3 = "";
  } else {
      $exit = true;
      echo "<b><font color=red>Invalid Type of Calibration Curve!</font></b><br/>";
  }

  // check if calibration curve with same name already exists for this standard
  $sql = "SELECT * FROM `calibration` WHERE name = '".$_POST["name"]."'";
  //$sql = "SELECT * FROM `calibration` WHERE `name` = 'PSU_TEMP_NUM_CAL' AND `idStandard` = ".$_POST["idStandard"];
  $result = $mysqli->query($sql);
  $num_rows = mysqli_num_rows($result);
  if ($result->num_rows > 0) {
      echo "<br/><b><font color=red>WE HAVE A PROBLEM!</font></b> Calibration Curve with same name (".$_POST["name"].") already exists.<br/>";
  } else {

  echo "<hr>";

  if (!$exit) {

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          if ($_POST["idCalCurve"]=="1") {
              $json3 .= "{\"xval\":\"".$array[0]."\",\"yval\":\"".$array[1]."\"},";
          } else if ($_POST["idCalCurve"]=="2") {
              $json3 .= "\"".$array[0]."\":\"".$array[1]."\",";
          } else if ($_POST["idCalCurve"]=="3") {
              $json3 .= "\"".$array[0]."\":\"".$array[1]."\",";
          }
      }
    }
    
    echo "<br/>";
    if ($_POST["idCalCurve"]=="1") {
        if ($rowNmb <> intval($_POST["ncurve"])) {
            echo "<font color=red>ncurve differs:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
        } else {
            echo "<font color=blue>ncurve is correct:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
        }
        $json1 = "\"ncurve\":\"".$rowNmb."\",\"inter\":\"".$_POST["inter"]."\",";
        $json3 = rtrim($json3, ",");
        $json4 = "]}";
    } else if ($_POST["idCalCurve"]=="2") {
        $json3 = rtrim($json3, ",");
        $json4 = "}";
    } else if ($_POST["idCalCurve"]=="3") {
        $json3 = rtrim($json3, ",");
        $json4 = "}";
    }
    $json = $json0.$json1.$json2.$json3.$json4;
    
    $cal = intval($_POST["idCalCurve"]) - 1;
    $sql = 
      "INSERT INTO ".
      "`calibration` ".
      "(`idStandard`, `type`, `name`, `shortDesc`, `setting`) ".
      "VALUES ".
      "(".$_POST["idStandard"].", ".$cal.", '".$_POST["name"]."', '".$_POST["shortDesc"]."', '".$json."')";
    echo "<font color=blue>".$sql."</font><br/>";
    
    $result = $mysqli->query($sql);
    echo "<br/><b>Result:</b> ".$result."<br/>";
    
    fclose($handle);
  }
  
  }  // END: else of if ($result->num_rows > 0)
  }  // END: if (!$exit)

  } else {  // use existing calibration
      echo "<br/>... <b>use existing calibration</b> ...<br/>";
      // get existing calibration curve
      $sql = "SELECT * FROM `calibration` WHERE id = ".$_POST["sel_calId"];
      $result = $mysqli->query($sql);
      $num_rows = mysqli_num_rows($result);
      if ($result->num_rows != 1) {
          echo "<br/><b><font color=red>WE HAVE A PROBLEM!</font></b> Calibration Curve with same name (".$_POST["name"].") already exists.<br/>";
      } else {
          $row = $result->fetch_assoc();
          echo "<br/><b>FOUND:</b> ".$row["name"]." / ".$row["shortDesc"]." (".$row["setting"].")<br/>";
          
          if ($_POST["idAction"]=="2") {  // Add new points/coefficients
              echo "<br/><b>Add new points/coefficients for Calibration Curve Type ".$_POST["idCalCurve"]."</b><br/><br/>";
              
              if ($_POST["idCalCurve"]=="1") {  // Numerical
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Numerical Calibration Curve not supported!</font></b><br/>";
              
                  $setting = substr($row["setting"], 0, -2);
                  $json3 = "";

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          $json3 .= "{\"xval\":\"".$array[0]."\",\"yval\":\"".$array[1]."\"},";
      }
	  
    }
    
    echo "<br/>";
    /*if ($rowNmb <> intval($_POST["ncurve"])) {
        echo "<font color=red>ncurve differs:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
    } else {
        echo "<font color=blue>ncurve is correct:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
    }*/
    //$json1 = "\"ncurve\":\"".$rowNmb."\",\"inter\":\"".$_POST["inter"]."\",";
    
    // increase ncurve
    $obj = json_decode($row["setting"]);
    $ncurve_old = $obj->{'ncurve'};
    echo "ncurve old: ".$ncurve_old."<br/>";
    $ncurve = $ncurve_old + $rowNmb;
    echo "ncurve new: ".$ncurve."<br/>";
    $setting = str_replace("\"ncurve\":\"".$ncurve_old."\"", "\"ncurve\":\"".$ncurve."\"", $setting);
    
    $json3 = rtrim($json3, ",");
    $json4 = "]}";
    //$json = $json0.$json1.$json2.$json3.$json4;
    $json = $setting.",".$json3.$json4;
    
    //$cal = intval($_POST["idCalCurve"]) - 1;
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    $result = $mysqli->query($sql);
    echo "<br/><b>Result:</b> ".$result."<br/>";
    
    fclose($handle);
  }


              } else if ($_POST["idCalCurve"]=="2") {  // Polynomial
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Polynomial Calibration Curve not supported!</font></b><br/>";
              
                  $obj = json_decode($row["setting"], TRUE);
                  foreach($obj as $key => $value) 
                    {
                      echo 'key: '.$key.' / value: '.$value.'<br/>';
                      $arr[$key] = $value;
                    }
                  print_r($arr);
                  echo "<br/>";
                  /*if (in_array('Pol1', $arr, true)) {
                      echo "'Pol1' found with strict check<br/>";
                  }*/
                  if (array_key_exists('Pol1', $arr)) {
                      echo "'Pol1' key found \n";
                  }
                  echo "<br/>";

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    $rowNmb = 0;
    $update = false;
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          echo "{\"key\":\"".$array[0]."\",\"value\":\"".$array[1]."\"}<br/>";
          
          // check if new coefficient already exists
          //if (in_array($array[0], $arr, true)) {
          if (array_key_exists($array[0], $arr)) {
              echo "<font color=red><b>Sorry, coefficient already exists!</b></font><br/>";
          } else {
              echo "<font color=blue><b>Coefficient will be added</b></font><br/>";
              $arr[$array[0]] = $array[1];
              $update = true;
          }
          
      }

    }
    echo $rowNmb." data found<br/>";
    
    echo "<br/>";
    print_r($arr);
    echo "<br/>";
    
    $json = "{";
    foreach($arr as $key => $value) {
        $json .= "\"$key\":\"$value\",";
    }
    $json = rtrim($json, ",");
    $json .= "}";
    echo "<font color=blue><b>".$json."</b></font><br/>";
    
    if ($update) {
    echo "<br/>";
    echo "<b>Database Manipulation: </b><br/>";
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    //$result = $mysqli->query($sql);
    //echo "<br/><b>Result:</b> ".$result."<br/>";
    }
    
    fclose($handle);
  }

              
              } else if ($_POST["idCalCurve"]=="3") {  // Logarithmical
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Logarithmical Calibration Curve not supported!</font></b><br/>";
              
                  $obj = json_decode($row["setting"], TRUE);
                  foreach($obj as $key => $value) 
                    {
                      echo 'key: '.$key.' / value: '.$value.'<br/>';
                      $arr[$key] = $value;
                    }
                  print_r($arr);
                  echo "<br/>";
                  /*if (in_array('Log1', $arr, true)) {
                      echo "'Log1' found with strict check\n";
                  }*/
                  if (array_key_exists('Log1', $arr)) {
                      echo "'Log1' key found \n";
                  }
                  echo "<br/>";

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    $rowNmb = 0;
    $update = false;
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          echo "{\"key\":\"".$array[0]."\",\"value\":\"".$array[1]."\"}<br/>";
          
          // check if new coefficient already exists
          //if (in_array($array[0], $arr, true)) {
          if (array_key_exists($array[0], $arr)) {
              echo "<font color=red><b>Sorry, coefficient already exists!</b></font><br/>";
          } else {
              echo "<font color=blue><b>Coefficient will be added</b></font><br/>";
              $arr[$array[0]] = $array[1];
              $update = true;
          }
          
      }

    }
    echo $rowNmb." data found<br/>";
    
        echo "<br/>";
    print_r($arr);
    echo "<br/>";
    
    $json = "{";
    foreach($arr as $key => $value) {
        $json .= "\"$key\":\"$value\",";
    }
    $json = rtrim($json, ",");
    $json .= "}";
    echo "<font color=blue><b>".$json."</b></font><br/>";
    
    if ($update) {
    echo "<br/>";
    echo "<b>Database Manipulation: </b><br/>";
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    //$result = $mysqli->query($sql);
    //echo "<br/><b>Result:</b> ".$result."<br/>";
    }
    
    fclose($handle);
  }
  
              
              
              }
          } else if ($_POST["idAction"]=="3") {  // Replace calibration
              echo "<br/><b>Replace calibration for Calibration Curve Type ".$_POST["idCalCurve"]."</b><br/><br/>";
              
              if ($_POST["idCalCurve"]=="1") {  // Numerical
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Numerical Calibration Curve not supported!</font></b><br/>";
      
                  $setting = explode("\"values\":", $row["setting"]);
                  $json3 = "";
              
  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          $json3 .= "{\"xval\":\"".$array[0]."\",\"yval\":\"".$array[1]."\"},";
      }
	  
    }
    
    echo "<br/>";
    /*if ($rowNmb <> intval($_POST["ncurve"])) {
        echo "<font color=red>ncurve differs:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
    } else {
        echo "<font color=blue>ncurve is correct:</font> calculated: ".$rowNmb.", given: ".$_POST["ncurve"]."<br/>";
    }*/
    //$json1 = "\"ncurve\":\"".$rowNmb."\",\"inter\":\"".$_POST["inter"]."\",";
    
    // increase ncurve
    $obj = json_decode($row["setting"]);
    $ncurve_old = $obj->{'ncurve'};
    echo "ncurve old: ".$ncurve_old."<br/>";
    $ncurve = $rowNmb;
    echo "ncurve new: ".$ncurve."<br/>";
    $setting = str_replace("\"ncurve\":\"".$ncurve_old."\"", "\"ncurve\":\"".$ncurve."\"", $setting[0]);
    
    $json3 = rtrim($json3, ",");
    $json4 = "]}";
    //$json = $json0.$json1.$json2.$json3.$json4;
    $json = $setting."\"values\":[".$json3.$json4;
    
    //$cal = intval($_POST["idCalCurve"]) - 1;
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    $result = $mysqli->query($sql);
    echo "<br/><b>Result:</b> ".$result."<br/>";
    
    fclose($handle);
  }
              
              
              } else if ($_POST["idCalCurve"]=="2") {  // Polynomial
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Polynomial Calibration Curve not supported!</font></b><br/>";
              
                  echo "<br/>";
                  echo "<b>Database: </b><br/>";
                  $obj = json_decode($row["setting"], TRUE);
                  foreach($obj as $key => $value) 
                    {
                      echo 'key: '.$key.' / value: '.$value.'<br/>';
                      $arr[$key] = $value;
                    }
                  print_r($arr);
                  echo "<br/>";
                  /*if (in_array('Pol1', $arr, true)) {
                      echo "'Pol1' found with strict check\n";
                  }*/
                  if (array_key_exists('Pol1', $arr)) {
                      echo "'Pol1' key found \n";
                  }
                  echo "<br/>";
                  
                  echo "<br/>";
                  echo "<b>File: </b><br/>";

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    $rowNmb = 0;
    $update = false;
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          echo "{\"key\":\"".$array[0]."\",\"value\":\"".$array[1]."\"}<br/>";
          
          // check if new coefficient already exists
          //if (in_array($array[0], $arr, true)) {
          if (array_key_exists($array[0], $arr)) {
              echo "<font color=blue><b>Coefficient will be replaced</b></font><br/>";
              $arr[$array[0]] = $array[1];
              $update = true;
          } else {
              echo "<font color=red><b>Sorry, coefficient does not exist!</b></font><br/>";
          }
          
      }

    }
    echo $rowNmb." data found<br/>";
    
    echo "<br/>";
    print_r($arr);
    echo "<br/>";
    
    $json = "{";
    foreach($arr as $key => $value) {
        $json .= "\"$key\":\"$value\",";
    }
    $json = rtrim($json, ",");
    $json .= "}";
    echo "<font color=blue><b>".$json."</b></font><br/>";
    
    if ($update) {
    echo "<br/>";
    echo "<b>Database Manipulation: </b><br/>";
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    //$result = $mysqli->query($sql);
    //echo "<br/><b>Result:</b> ".$result."<br/>";
    }
    
    fclose($handle);
  }

              
              
              } else if ($_POST["idCalCurve"]=="3") {  // Logarithmical
              
      echo "<br/>";
      if ($exit) echo "<b><font color=red>Logarithmical Calibration Curve not supported!</font></b><br/>";
              
                  $obj = json_decode($row["setting"], TRUE);
                  foreach($obj as $key => $value) 
                    {
                      echo 'key: '.$key.' / value: '.$value.'<br/>';
                      $arr[$key] = $value;
                    }
                  print_r($arr);
                  echo "<br/>";
                  /*if (in_array('Log1', $arr, true)) {
                      echo "'Log1' found with strict check\n";
                  }*/
                  if (array_key_exists('Log1', $arr)) {
                      echo "'Log1' key found \n";
                  }
                  echo "<br/>";

  if (($handle = fopen($target_file, "r")) !== FALSE) {
    //while (($data = fgetcsv($handle, 1024, $csvDelimiter)) !== FALSE) {
    $rowNmb = 0;
    $update = false;
    while (($data = fgets($handle)) !== FALSE) {
      //$num = count($data);
      //echo "Nr.: ".$num."<br/>";
      //echo "dat[0]: ".$data[0]."<br/>";
      echo "<b>row:</b> ".$data."<br/>";
      if (substr($data, 0, 1) == "#") {
          echo "&nbsp;&nbsp;=> comment found<br/>";
      } else {
          $rowNmb += 1;
          $output = preg_replace('/\s+/', ' ',trim($data));
          $array = explode(" ", $output);
          $num = count($array);
          echo "&nbsp;&nbsp;=> Nr.: ".$num."<br/>";
          echo "{\"key\":\"".$array[0]."\",\"value\":\"".$array[1]."\"}<br/>";
          
          // check if new coefficient already exists
          //if (in_array($array[0], $arr, true)) {
          if (array_key_exists($array[0], $arr)) {
              echo "<font color=blue><b>Coefficient will be replaced</b></font><br/>";
              $arr[$array[0]] = $array[1];
              $update = true;
          } else {
              echo "<font color=red><b>Sorry, coefficient does not exist!</b></font><br/>";
          }
          
      }

    }
    echo $rowNmb." data found<br/>";

    echo "<br/>";
    print_r($arr);
    echo "<br/>";
    
    $json = "{";
    foreach($arr as $key => $value) {
        $json .= "\"$key\":\"$value\",";
    }
    $json = rtrim($json, ",");
    $json .= "}";
    echo "<font color=blue><b>".$json."</b></font><br/>";
    
    if ($update) {
    echo "<br/>";
    echo "<b>Database Manipulation: </b><br/>";
    $sql =
      "UPDATE ".
      "`calibration` ".
      "SET ".
      "`setting` = '".$json."' ".
      "WHERE ".
      "id = ".$_POST["sel_calId"];
    echo "<font color=blue>".$sql."</font><br/>";
    
    //$result = $mysqli->query($sql);
    //echo "<br/><b>Result:</b> ".$result."<br/>";
    }

    fclose($handle);
  }
  
              
              
              }
          } else {
              echo "<b><font color=red>Invalid Action: </font></b> ".$_POST["idAction"]."<br/>";
          }
      }
  }  // END: use existing calibration

}  // END: if($imageFileType == "dat" OR $imageFileType == "csv")

?>


<br/><br/>


				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					<a class="a_btn" href="open_application.php?idProject=<?php echo $idProject; ?>&idApplication=<?php echo $idApplication; ?>" target="_self">>> BACK <<</a>
					<br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>