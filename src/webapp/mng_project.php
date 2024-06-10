<?php

require "utils/session_utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';

session_start();
check_session();

$database = new Database();

$users = $database->select("SELECT id, name, email FROM `user` ORDER BY name");



$sidebar_actions = [ ["label" => "Back", "link" => "index.php"],
					 ["label" => "Home", "link" => "index.php"]];

$pagetitle = "Projects";
$site_js = "js/mng_project.js";
$tpl = "mng_project.tpl.php";
include "template.php";


?>
