<?php

require "utils/session_utils.php";

session_start();
check_session();


$pagetitle = "SPRINT";
$is_admin = $_SESSION['is_admin'];
$site_css = "layout/index.css";
$tpl = "index.tpl.php";
include "template.php";

?>
