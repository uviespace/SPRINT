<?php

require_once "BaseController.php";
require_once "ProjectController.php";
require_once "FunctionController.php";
require_once "api_session_utils.php";
require_once "Router.php";

define("EMPTY", 0);
define("API_INDEX", 2);
define("VERSION_INDEX", 3);
define("PROJECT_INDEX", 4);
define("PROJECT_ID_INDEX", 5);
define("SUB_ITEM_INDEX", 6);
define("SUB_ITEM_ID", 7);
define("SUB_SUB_ITEM_INDEX", 8);
define("SUB_SUB_ITEM_ID_INDEX", 9);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//$uri = explode('/', $uri);

session_start();
check_session();

// do routing

$router = new Router();


// Projects

$router->get("api/v2/projects/:project_id", function($route_ids) {
	$projectController = new ProjectController();
	$projectController->get_project($route_ids["project_id"]);
});


// Projects sub path

route_crud($router, "api/v2/projects/:project_id/standards", "standard_id", new StandardsController(), "project_id");
route_crud($router, "api/v2/projects/:project_id/applications", "application_id", new ApplicationController(), "project_id");
route_crud($router, "api/v2/projects/:project_id/apids", "apid_id", new ApidController(), "project_id");

// Standards sub path

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/tcheaders", "tcheader_id", new TCHeaderController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/tmheaders", "tmheader_id", new TMHeaderController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/services", "service_id", new ServiceController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets", "packet_id", new PacketController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/constants", "constant_id", new ConstantController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/datatypes", "datatype_id", new DatatypesController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/datapool", "datapool_id", new DatapoolController(), "standard_id");
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/parameters", "parameter_id", new ParameterController(), "standard_id");

// Packets sub-sub path

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/parameters",
		   "parameter_id", new PacketParameterController(), "packet_id");


$router->get("api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/header_size",
			 function($route_ids) {
				 $functionController = new FunctionController();
				 $functionController->get_header_size($route_ids["standard_id"], $route_ids["packet_id"]);
});


$router->resolve($uri);


function route_crud($router, $end_point, $id_name, $crudController, $controller_id)
{
	$router->get($end_point, function($route_ids) use ($crudController, $controller_id) {
		try {
			$crudController->get_items($route_ids[$controller_id]);
		} catch (Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->post($end_point, function($route_ids) use ($crudController, $controller_id) {
		try {
			$crudController->create_item($route_ids[$controller_id], json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->put($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $controller_id) {
		try {
			if (!check_user_can_write_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->put_item($route_ids[$controller_id], json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->delete($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $controller_id, $id_name) {
		try {
			if (!check_user_can_delete_project($project_id))
				$crudController->forbidden();
			
			$crudController->delete_item($route_ids[$controller_id], $route_ids[$id_name]);
		} catch (Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
}


/*
   if (count($uri) < 6) {
   $baseController->not_found();
   }

   $base_path = $uri[PROJECT_INDEX];

   try {

   switch($base_path) {
   case "projects":
   $project_id = $uri[PROJECT_ID_INDEX];
   
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
   
   switch($uri[SUB_ITEM_INDEX]) {
   // api/v2/projects/{project_id}/{sub_item}
   case "standards":
   route_project_item($baseController, new StandardsController(), $uri, $project_id);
   break;
   case "applications":
   route_project_item($baseController, new ApplicationController(), $uri, $project_id);
   break;
   case "apids":
   route_project_item($baseController, new ApidController(), $uri, $project_id);
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
   // api/v2/projects/{project_id}/{sub_item}/{sub_item_id}
   $sub_item_id = $uri[SUB_ITEM_ID];
   
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
   } else if ($uri[SUB_ITEM_INDEX] == "standards" && count($uri) > 8) {
   // api/v2/projects/{project_id}/standards/{standard_id}/{sub_sub_item}
   switch($uri[SUB_SUB_ITEM_INDEX]) {
   case "tcheaders":
   route_sub_project_item($baseController, new TCHeaderController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "tmheaders":
   route_sub_project_item($baseController, new TMHeaderController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "services":
   route_sub_project_item($baseController, new ServiceController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "packets":
   route_sub_project_item($baseController, new PacketController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "constants":
   route_sub_project_item($baseController, new ConstantController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "datatypes":
   route_sub_project_item($baseController, new DatatypesController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "datapool":
   route_sub_project_item($baseController, new DatapoolController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   case "parameters":
   route_sub_project_item($baseController, new ParameterController(), $uri, $project_id, $uri[SUB_ITEM_ID]);
   break;
   default:
   $baseController->not_found();
   }
   } else {
   // For now
   $baseController->not_found();
   }
   }


   function route_sub_project_item($baseController, $crudController, $uri, $project_id, $standard_id)
   {
   if (count($uri) == 9 && $_SERVER['REQUEST_METHOD'] == "GET") {
   $crudController->get_items($standard_id);
   } else if (count($uri) == 9 && $_SERVER['REQUEST_METHOD'] == "POST") {
   $crudController->create_item($standard_id, json_decode(file_get_contents("php://input")));
   } else if (count($uri) == 10) {
   // api/v2/projects/{project_id}/{sub_item}/{sub_item_id}/{sub_sub_item}/{sub_sub_item_id}
   $item_id = $uri[SUB_SUB_ITEM_ID_INDEX];

   switch($_SERVER['REQUEST_METHOD']) {
   case "POST":
   $baseController->not_found();
   break;
   case "PUT":
   if (!check_user_can_write_project($project_id))
   $baseController->forbidden();

   $crudController->put_item($standard_id, json_decode(file_get_contents("php://input")));
   break;
   case "DELETE":
   if (!check_user_can_delete_project($project_id))
   $baseController->forbidden();

   $crudController->delete_item($standard_id, $item_id);
   break;

   default:
   $baseController->not_found();
   }
   } else if (count($uri) == 11 && $uri[SUB_SUB_ITEM_INDEX] == "packets" &&  $uri[10] == "packet_size") {
   $item_id = $uri[SUB_SUB_ITEM_ID_INDEX];
   $functionController = new FunctionController();
   $functionController->get_packet_size($standard_id, $item_id);
   } else {
   $baseController->not_found();
   }

   }*/

?>
