<?php
// https://www.w3schools.com/php/php_file_upload.asp
// https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (isset($_POST["idProject"])) { $idProject  = $_POST["idProject"]; } else { $idProject=0; };
if (isset($_POST["idApplication"])) { $idApplication  = $_POST["idApplication"]; } else { $idApplication=0; };
if (isset($_POST["dpDomain"])) { $dpDomain  = $_POST["dpDomain"]; } else { $dpDomain=""; };

echo "Project ID: ".$idProject."<br/>";
echo "Application ID: ".$idApplication."<br/>";
echo "DP Domain: ".$dpDomain."<br/>";

// Check if image file is a actual image or fake image
if(isset($_POST["importDpList"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".<br/>";
    echo $target_file."<br/>";
    $uploadOk = 0;
    $messageDpImport = $target_file;
  } else {
    echo "File is not an image.<br/>";
    $uploadOk = 1;
    $messageDpImport = "file detected.<br/>";
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.<br/>";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "csv") {
  echo "Sorry, only JPG, JPEG, PNG, GIF & CSV files are allowed.<br/>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.<br/>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.<br/>";
  } else {
    echo "Sorry, there was an error uploading your file.<br/>";
  }
}

// read CSV file
if($imageFileType == "csv") {
  echo "Read CSV file...<br/>";
  $row = 1;
  if (($handle = fopen($target_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $num = count($data);
      echo "<p> $num fields in line $row: <br /></p>\n";
      $row++;
      for ($c=0; $c < $num; $c++) {
        echo $data[$c] . "<br />\n";
      }
    }
    fclose($handle);
  }
}

// rename imported CSV file

//header('Location: open_application.php?idProject='.$idProject.'&idApplication='.$idApplication);
?>