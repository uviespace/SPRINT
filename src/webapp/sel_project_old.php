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
require 'int/config.php';

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$userName = $row["name"];
$userEmail = $row["email"];

if(isset($_POST['import'])){
    //echo "IMPORT<br/>";

   if(isset($_FILES['importfile'])){
      $errors= array();
	  
	  $files = array_filter($_FILES['importfile']['name']); //Use something similar before processing files.
	  // Count the number of uploaded files in array
      $total_count = count($_FILES['importfile']['name']);
	  $message = "count: ".$total_count;
	  
	  $timestamp = time();
      $datum = date("YmdHis", $timestamp);
	  $dir_of_imported_project = $path_to_imports."Project_".$datum;
	  mkdir($dir_of_imported_project, 0700);
	  
	  $extensions = array("csv","txt");
			  
	  // Loop through every file
      for( $i=0 ; $i < $total_count ; $i++ ) {
          //The temp file path is obtained
          $tmpFilePath = $_FILES['importfile']['tmp_name'][$i];
		  //Check 
          $file_name = $_FILES['importfile']['name'][$i];
          $file_size =$_FILES['importfile']['size'][$i];
          $file_tmp =$_FILES['importfile']['tmp_name'][$i];
          $file_type=$_FILES['importfile']['type'][$i];
          $file_name_explode = explode('.',$_FILES['importfile']['name'][$i]);
          $file_ext=strtolower(end($file_name_explode));
      
	      if(!file_exists($file_tmp)) {
              $errors[]="No file selected. Please choose the file first!";
	      } else {
              if(in_array($file_ext,$extensions)=== false){
                 $errors[]=$file_name.": extension not allowed, please choose a CSV or TXT file.";
              }
	      }
		  
          //A file path needs to be present
          if ($tmpFilePath != ""){
              //Setup our new file path
              $newFilePath = $dir_of_imported_project."/" . $_FILES['importfile']['name'][$i];
              //File is uploaded to temp dir
              if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                  //Other code goes here
			  }
          }
      }
	  
      if(empty($errors)==true){
          //echo "Success";
          
          $cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."import_csv.py project ".$userid." ".$path."Project_".$datum." 2>&1";
          
          $res = shell_exec($cmd);
          //$message = $res;
		  $message .= " | Import successful";
 
      }else{
          //print_r($errors);
      }
      
   }

}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Projects</title>
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
	<link rel="stylesheet" type="text/css" href="int/layout.css">
    <script type="text/javascript" src="int/config.js"></script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
	<script type="text/javascript">
        
        var infoarea;
        
        window.onload=function(){
            var el = document.getElementById( 'file-upload' );
            infoArea = document.getElementById( 'file-upload-filename' );
            
            if(el){
                el.addEventListener( 'change', showFileName );
            }
            
        }
        
        function showFileName( event ) {

            // the change event gives us the input it occurred in 
            var input = event.srcElement;
            
            // the input has an array of files in the `files` property, each one has a name that you can use. We're just using the name here.
            var fileName = input.files[0].name;
            
            // use fileName however fits your app best, i.e. add it into a div
            infoArea.textContent = 'File name: ' + fileName;
			
        }
        
	</script>
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Projects</h2>
		        </div>
				
				<!--
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
				-->
		    </div>
		</div>

		<form  method="post" style="background-color: #d1d1d1; padding: 15px;" enctype="multipart/form-data">
            <input type="file" id="file-upload" name="importfile[]" multiple="multiple" style="display:none" />
            <label for="file-upload" class="btn btn-primary browse-file">Choose File</label>
            <input type="submit" name="import" value="Import" class="btn btn-success crud-submit-import">
            <div id="file-upload-filename"></div>
            <?php
                if(empty($errors)==false){
                    echo "<font color='red'>";
                    print_r($errors);
                    echo "</font><br/>";
                }
                if(empty($message)==false){
                    echo "<font color='green'>";
                    print_r($message);
                    echo "</font><br/>";
                }
            ?>
		</form>
		
		<br/>

<?php

//require 'api/db_config.php';

if ($userid == 1 || $userid == 1001) {
    $sql = "SELECT * FROM `project` ORDER BY `id`";
} else {
    $sql = "SELECT p.* ".
           "FROM `project` p ".
           "LEFT JOIN `userproject` up ".
           "ON p.id = up.idProject ".
           "WHERE p.isPublic = 1 ".
           "OR up.idRole = 1 ".
           "OR (up.idUser = ".$userid." ".
           "AND up.idRole = 2) ".
           "OR (up.email = '".$userEmail."' ".
           "AND (up.idRole = 3 OR up.idRole = 4)) ".
           "ORDER BY p.id";
}

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

echo "$num_rows hits<br/><br/>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Description: " . $row["desc"]. "<br/>";
        echo "<div style='height:30px; padding:5px; width:50%; background-color:lightblue;'>";
        echo "<a href='open_project.php?id=".$row["id"]."' >".$row["id"]." <b>".$row["name"]."</b></a>";
        if ($row["isPublic"] == 1) {
            echo "<span style='float:right;'><img src='img/isPublic20.png' />&nbsp;</span>";
        }
        echo "</div>";
        echo "<br/>";
    }
} else {
    echo "0 results";
}


?>

<!--
				<div>
					<br/>
					<a href="tab_project.php" target="_self">Table project</a><br/>
					<br/>
				    <a href="tab_application.php" target="_self">Table application</a><br/>
					<a href="tab_standard.php" target="_self">Table standard</a><br/>
					<br/>
					<a href="tab_service.php" target="_self">Table service</a><br/>
					<a href="tab_packet.php" target="_self">Table packet</a><br/>
					<a href="tab_parameter.php" target="_self">Table parameter</a><br/>
					<a href="tab_type.php" target="_self">Table type</a><br/>
					<a href="tab_constants.php" target="_self">Table constants</a><br/>
					<br/>
					<a href="tab_enumeration.php" target="_self">Table enumeration</a><br/>
					<a href="tab_limit.php" target="_self">Table limit</a><br/><br/>
					Data Pool (from Table parameter)
					<br/>
					<a href="tab_packet_test.php" target="_self">Table packet NEW</a><br/>
					<br/>
					<br/>
					<a href="mng_project.php" target="_self">Manage my projects...</a><br/>
					<a href="open_project.php" target="_self">Open project</a><br/>
				</div>
-->
				<div class="topcorner_left">
<?php include 'logos.php'; ?>
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

				</div>

	</div>
</body>

</html>