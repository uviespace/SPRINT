<?php

require_once "BaseController.php";
require_once "../../db/Database.php";

function check_session() {
	$baseController = new BaseController();
	if (!isset($_SESSION['userid'])) {
		$baseCotroller->forbidden();
		die('');
	}
}

function check_user_can_access_project($project_id) {
	$database = new Database();

	$user = $database->select("SELECT id, idRole FROM userproject WHERE idUser = ? AND idProject = ?", ["ii", [$_SESSION['userid'], $project_id]]);

	return count($user) > 0 || $_SESSION['is_admin'];
}

function check_user_can_delete_project($project_id) {
	$database = new Database();

	$user = $database->select("SELECT up.id, up.idRole " .
							  "FROM userproject up INNER JOIN `role` r ON up.idRole = r.id " .
							  "WHERE idUser = ? AND idProject = ? AND permissionDelete = 1 " .
							  "UNION " .
							  "SELECT up.id, up.idRole " .
							  "FROM userproject up INNER JOIN `role` r ON up.idRole = r.id INNER JOIN `user` u ON u.email = up.email " .
							  "WHERE u.id = ? AND idProject = ? AND r.permissionDelete  = 1",
							  ["iiii", [$_SESSION['userid'], $project_id, $_SESSION['userid'], $project_id]]);

	return count($user) > 0 || $_SESSION['is_admin'];

}

function check_user_can_write_project($project_id) {
	$database = new Database();

	$user = $database->select("SELECT up.id, up.idRole " .
							  "FROM userproject up INNER JOIN `role` r ON up.idRole = r.id " .
							  "WHERE idUser = ? AND idProject = ? AND permissionWrite = 1 " .
							  "UNION " .
							  "SELECT up.id, up.idRole " .
							  "FROM userproject up INNER JOIN `role` r ON up.idRole = r.id INNER JOIN `user` u ON u.email = up.email " .
							  "WHERE u.id = ? AND idProject = ? AND r.permissionWrite  = 1",
							  ["iiii", [$_SESSION['userid'], $project_id, $_SESSION['userid'], $project_id]]);

	return count($user) > 0 || $_SESSION['is_admin'];
}

?>
