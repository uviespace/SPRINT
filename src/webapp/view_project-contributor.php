<?php

require_once "utils/session_utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';
require_once 'int/config.php';
require_once 'utils/utils.php';

session_start();
check_session();


if (!isset($_GET["idProject"])) {
	http_response_code(403);
	die('Forbidden');
}

$database = new Database();

$project = $database->select("SELECT name FROM project WHERE id = ?",["i", [$_GET["idProject"]]])[0]["name"];


$emails = $database->select("SELECT id, email FROM `user` u ORDER BY email");


$sidebar_actions = [
	[ "label" => "Back", "link" => "open_project.php?id=" . $_GET["idProject"]  ],
	[ "label" => "Home", "link" => "index.php"]
];

$pagetitle = "Contributors";
$site_js = "js/view_project-contributor.js";
$tpl = "view_project-contributor.tpl.php";
include "template.php";

?>
