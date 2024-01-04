<?php

require "utils/session_utils.php";
require "api/db_config.php";
require "int/global_functions.php";

session_start();
check_session();

$pagetitle = "CORDET Editor - Parameters";
$username = $_SESSION['username'];
$site_css = "site_js/view-parameter.js";
$tpl = "view_parameter.tpl.php";
include "template.php";




?>
