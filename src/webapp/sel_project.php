<?php

require "utils/session_utils.php";
require 'db/db_config.php';
require 'db/Database.php';
require 'int/config.php';

session_start();
check_session();

$database = new Database();


$projects = $database->select("SELECT p.id, p.name, p.`desc`, p.isPublic " . 
							  "FROM `project` p " .
							  "LEFT JOIN `userproject` up ON p.id = up.idProject " .
							  "WHERE p.isPublic = 1 " . 
							  "OR up.idRole = 1 " .
							  "OR (up.idUser = ? AND up.idRole = 2) " .
							  "OR (up.email = ? AND (up.idRole = 3 OR up.idRole = 4)) " .
							  "OR (? = 1 OR ? = 1001) " .
							  "GROUP BY id, name, `desc`, isPublic " .
							  "ORDER BY p.id",
							  ["isii", [$_SESSION['userid'], $_SESSION['email'], $_SESSION['userid'], $_SESSION['userid']]]);


if (isset($_POST['import']))
	list($message, $errors) = import_project($_SESSION['userid'], $python_settings);


// Template settings
$pagetitle = "Projects";
$sidebar_actions = [ ["label" => "Home", "link" => "index.php"] ];
$site_css = "layout/sel_project.css";
$tpl = "sel_project.tpl.php";
include "template.php";



function import_project($userid, $python_settings)
{

	$message = "";
	$errors = [];
	
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
			$file_size = $_FILES['importfile']['size'][$i];
			$file_tmp = $_FILES['importfile']['tmp_name'][$i];
			$file_type = $_FILES['importfile']['type'][$i];
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
			
			//$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."import_csv.py project ".$userid." ".$path."Project_".$datum." 2>&1";
			$cmd = $python_settings["cmd"] . " " . $python_settings["script_path"] .
				   "import_csv.py project " . $userid . " " . $python_settings["path"] .
				   "Project_" . $datum . " 2>&1";
			
			$res = shell_exec($cmd);
			//$message = $res;
			$message .= " | Import successful";
			
		}else{
			//print_r($errors);
		}
		
	}

	return array($message, $errors);

}


?>
