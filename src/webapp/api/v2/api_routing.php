<?php

require_once "BaseController.php";
require_once "ProjectController.php";
require_once "FunctionController.php";
require_once "UserController.php";
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

// Users
route_crud_admin($router, "api/v2/users", "user_id", new UserController());


// Projects




/*$router->get("api/v2/projects/:project_id", function($route_ids) {
	$projectController = new ProjectController();
	$projectController->get_project($route_ids["project_id"]);
});
 */

route_crud($router, "api/v2/projects", "project_id", new ProjectController());


// Projects sub path

route_crud($router, "api/v2/projects/:project_id/standards", "standard_id", new StandardsController());
route_crud($router, "api/v2/projects/:project_id/applications", "application_id", new ApplicationController());
route_crud($router, "api/v2/projects/:project_id/apids", "apid_id", new ApidController());
route_crud($router, "api/v2/projects/:project_id/contributors", "userproject_id", new ContributorController());

// Standards sub path

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/tcheaders", "tcheader_id", new TCHeaderController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/tmheaders", "tmheader_id", new TMHeaderController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/services", "service_id", new ServiceController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets", "packet_id", new PacketController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/constants", "constant_id", new ConstantController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/datatypes", "datatype_id", new DatatypesController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/datapool", "datapool_id", new DatapoolController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/parameters", "parameter_id", new ParameterController());
route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/calibration", "calibration_id", new CalibrationController());


// Packets sub-sub path

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/parameters",
		   "parameter_id", new PacketParameterController());

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/derived_packets",
		   "child_id", new DerivedPacketController());

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/derived_packets/:child_id/parameters",
		   "parameter_id", new DerivedPacketParameterController());

route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/datatypes/:datatype_id/enumerations",
		   "enum_id", new EnumerationController());


route_crud($router, "api/v2/projects/:project_id/standards/:standard_id/parameters/:parameter_id/limits",
		   "limit_id", new LimitController());


$router->get("api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/header_size",
			 function($route_ids) {
				 if (!check_user_can_access_project($route_ids["project_id"]))
					 $crudController->forbidden();
				 
				 $functionController = new FunctionController();
				 $functionController->get_header_size($route_ids["standard_id"], $route_ids["packet_id"]);
});

$router->get("api/v2/projects/:project_id/standards/:standard_id/packets/:packet_id/derived_packets/:child_id/parent_size",
			 function($route_ids) {
				 if (!check_user_can_access_project($route_ids["project_id"]))
					 $crudController->forbidden();
				 
				 $functionController = new FunctionController();
				 $functionController->get_parent_size($route_ids["standard_id"], $route_ids["packet_id"]);
});


$router->post("api/v2/projects/:project_id/standards/:standard_id/parameters/:parameter_id/calibration_curve/:curve_id",
			  function($route_ids) {
				  if (!check_user_can_write_project($route_ids["project_id"]))
					  $crudController->forbidden();
				  
				  $functionController = new FunctionController();
				  $functionController->set_calibration_curve_to_parameter($route_ids["parameter_id"], $route_ids["curve_id"]);
});


$router->resolve($uri);


function route_crud_admin($router, $end_point, $id_name, $crudController)
{
	$router->get($end_point, function($route_ids) use ($crudController) {
		try {
			if (!$_SESSION['is_admin'])
				$crudController->forbidden();

			$crudController->get_items($route_ids);
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});

	$router->post($end_point, function($route_ids) use ($crudController) {
		try {
			if (!$_SESSION['is_admin'])
				$crudController->forbidden();
			
			$crudController->create_item($route_ids, json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});


	$router->get($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $id_name) {
		try {
			if (!$_SESSION['is_admin'])
				$crudController->forbidden();
			
			$crudController->get_item($route_ids, $route_ids[$id_name]);
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
		
	});
	
	$router->put($end_point . "/:" . $id_name, function($route_ids) use ($crudController) {
		try {
			if (!$_SESSION['is_admin'])
				$crudController->forbidden();
			
			$crudController->put_item($route_ids, json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->delete($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $id_name) {
		try {
			if (!$_SESSION['is_admin'])
				$crudController->forbidden();
			
			$crudController->delete_item($route_ids, $route_ids[$id_name]);
		} catch (Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
}

function route_crud($router, $end_point, $id_name, $crudController)
{
	$router->get($end_point, function($route_ids) use ($crudController) {
		try {
			if (array_key_exists("project_id", $route_ids) && !check_user_can_access_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->get_items($route_ids);
		} catch (Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->post($end_point, function($route_ids) use ($crudController) {
		try {
			if (array_key_exists("project_id", $route_ids) && !check_user_can_write_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->create_item($route_ids, json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});


	$router->get($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $id_name) {
		try {
			if (!check_user_can_access_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->get_item($route_ids, $route_ids[$id_name]);
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
		
	});
	
	$router->put($end_point . "/:" . $id_name, function($route_ids) use ($crudController) {
		try {
			if (!check_user_can_write_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->put_item($route_ids, json_decode(file_get_contents("php://input")));
		} catch(Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
	
	$router->delete($end_point . "/:" . $id_name, function($route_ids) use ($crudController, $id_name) {
		try {
			if (!check_user_can_delete_project($route_ids["project_id"]))
				$crudController->forbidden();
			
			$crudController->delete_item($route_ids, $route_ids[$id_name]);
		} catch (Exception $e) {
			$crudController->send_output(json_encode(array("Error" => $e->getMessage())) , array("Http/1.1 500 Internal Server Error"));
		}
	});
}

?>
