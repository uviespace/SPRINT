<?php

require_once "BaseController.php";
require_once "ProjectController.php";
require_once "api_session_utils.php";

$baseController = new BaseController();
$projectController = new ProjectController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

session_start();
check_session();

// do routing

if (count($uri) < 6) {
	$baseController->not_found();
}

$base_path = $uri[4];

try {

	switch($base_path) {
		case "projects":
			$project_id = $uri[5];
			
			if (!is_numeric($project_id))
				$baseController->not_found();
			else
				route_projects($baseController, $projectController, $uri, $project_id);
			break;
		default:
			$baseController->not_found();
			break;
	}
} catch(Exception $e) {
	$baseController->send_error($e->getMessage());
}

	
function route_projects($baseController, $projectController, $uri, $project_id)
{
	if (!check_user_can_access_project($project_id)) {
		$baseController->forbidden();
		return;
	}
	
	if (count($uri) == 6) {
		$projectController->get_project($project_id);
		return;
	}
	
	switch($uri[6]) {
			// api/v2/projects/{project_id}/standards
		case "standards":
			route_project_item($baseController, new StandardsController(), $uri, $project_id);
			break;
		case "applications":
			route_project_item($baseController, new ApplicationController(), $uri, $project_id);
			break;
		default:
			$baseController->not_found();
			break;
	}
}

function route_project_item($baseController, $crudController, $uri, $project_id)
{
	if (count($uri) == 7 && $_SERVER['REQUEST_METHOD'] == "GET") {
		$crudController->get_items($project_id);
	} else if (count($uri) == 7 && $_SERVER['REQUEST_METHOD'] == "POST") {
		$crudController->create_item($project_id, json_decode(file_get_contents("php://input")));
	} else if (count($uri) == 8) {
		// api/v2/projects/{project_id}/standards/{standard_id}
		$sub_item_id = $uri[7];
		
		switch($_SERVER['REQUEST_METHOD']) {
			case "POST":
				$baseController->not_found();
				break;
			case "PUT":
				if (!check_user_can_write_project($project_id))
					$baseController->forbidden();
				
				$crudController->put_item($project_id, json_decode(file_get_contents("php://input")));
				break;
			case "DELETE":
				if (!check_user_can_delete_project($project_id))
					$baseController->forbidden();
				
				$crudController->delete_item($project_id, $sub_item_id);
				break;
			default:
				$baseController->not_found();
				break;
		}
	} else {
		// For now
		$baseController->not_found();
	}
}



?>
