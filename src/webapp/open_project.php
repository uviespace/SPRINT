<?php

require "utils/session_utils.php";
require 'db/db_config.php';
require 'db/Database.php';
require_once 'int/config.php';
require_once 'utils/utils.php';

session_start();
check_session();

if (!isset($_GET["id"])) {
	http_response_code(403);
	die('Forbidden');
}

$database = new Database();

# Check if user can acces project

if (!check_user_can_access_project($_GET['id'])) {
	http_response_code(403);
	die('Forbidden');
}

$project = $database->select("SELECT name FROM project WHERE id = ?", ["i", [$_GET['id']]]);

$project_name = $project[0]["name"];
$pagetitle = "Project " . $project_name;

//$standards = $database->select("SELECT id, name, desc FROM standard WHERE idProject = ?", ["i", [$_GET['id']]]);

$acronyms = $database->select("SELECT count(*) as cnt FROM projectacronym WHERE idProject = ?", ["i", [$_GET['id']]]);
$acronym_cnt = $acronyms[0]['cnt'];

$documents = $database->select("SELECT count(*) as cnt FROM projectdocument WHERE idProject = ?", ["i", [$_GET['id']]]);
$document_cnt = $documents[0]['cnt'];

$internal_requirements = $database->select("SELECT count(*) as cnt FROM projectrequirement WHERE idProject = ?", ["i", [$_GET['id']]]);
$int_req_cnt = $internal_requirements[0]['cnt'];

# TODO: fix this in the database
$userrole = $database->select("SELECT idRole FROM userproject WHERE idProject=? AND idUser = ?", ["ii", [$_GET["id"], $_SESSION['userid']]]);
$id_role = count($userrole) > 0 ? $userrole[0]['idRole'] : 5;

if ($_SESSION["is_admin"])
	$id_role = 2;

#TODO: fix this in the database. What the hell is this data model
$contributors = $database->select("SELECT u.id, u.name, up.email, up.idRole  " .
								  "FROM userproject up INNER JOIN `user` u ON u.id = up.idUser " .
								  "WHERE up.idRole IN (3,4) AND up.idProject = ?", ["i", [$_GET['id']]]);

$owner = $database->select("SELECT u.id, u.name, u.email " .
						   "FROM userproject up INNER JOIN `user` u ON u.id = up.idUser " .
						   "WHERE up.idRole = 2 AND up.idProject = ?", ["i", [$_GET['id']]]);


# Post actions
$errors = [];
if (isset($_POST['export']))
	list($message, $errors) = export_project($_GET['id'], $python_settings);

if (isset($_POST['import']))
	list($message, $errors) = import_project($_GET['id'], $python_settings);

# Template settings
$sidebar_actions = [ ["label" => "Back", "link" => "sel_project.php" ], ["label" => "Home", "link" => "index.php"]  ];
$site_css = "layout/open_project.css";
$site_js = "js/open_project.js";
$tpl = "open_project.tpl.php";
include "template.php";


function export_project($idProject, $python_settings)
{
	$cmd = $python_settings["cmd"] . " " . $python_settings["script_path"] .
		   "export_csv.py project " . $idProject . " 2>&1";

	$file = shell_exec($cmd);
	$folder_to_delete = pathinfo($file, PATHINFO_DIRNAME);
	$file = substr($file, 0, strlen($file)-1);
	$message = "";
	$errors = [];
	
	if (file_exists($file)) {
		$messaage = $file;
		lib_dwnFile(true, $file);
		rmdirr($fileToDelete);
	} else {
		$errors[]  = "Error: Consistency check failed!\n";
		$errors[] .= "Please correct the errors as listed hereafter:\n";
		$errors[] .= $file;
	}

	return array($message, $errors);
}

function import_project($idProject, $python_settings)
{
	if(isset($_POST['import'])){

		if(isset($_FILES['importfile'])){

			$errors = [];
			$files = array_filter($_FILES['importfile']['name']); //Use something similar before processing files.
			// Count the number of uploaded files in array
			$total_count = count($_FILES['importfile']['name']);
			$message = "count: ".$total_count;
			
			$timestamp = time();
			$datum = date("YmdHis", $timestamp);
			$dir_of_imported_project = $python_settings["import_path"]."Standard_".$datum;
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
				
				//$cmd = $path_to_python.$python_cmd." ".$path_to_pyscripts."import_csv.py standard ".$idProject." ".$path."Standard_".$datum." 2>&1";
				$cmd = $python_settings["cmd"] . " " . $python_settings["script_path"] .
					   "import_csv.py standard " . $idProject . " " . $python_settings["path"] .
					   "Standard_" . $datum . " 2>&1";
				
				$res = shell_exec($cmd);
				$message = $res;
				//$message .= " | Import successful";
				
			}else{
				//print_r($errors);
			}
			
		}

	}
	
	return array($message, $errors);
}


?>
