<?php

function check_session() {
	if (!isset($_SESSION['userid'])) {
		// TODO: send with returnUrl to login to redirect back afterwards
		header("refresh:0,url=login.php");
		die('');
	}
}

function check_user_can_access_project($project_id) {
	$database = new Database();
	
	$user = $database->select("SELECT isPublic, u.id, u.idRole " .
							  "FROM project p LEFT JOIN userproject u ON u.idProject = p.id " .
							  "WHERE idProject = ? AND (idUser = ? OR p.isPublic) ", ["ii", [$project_id, $_SESSION['userid']]]);

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
