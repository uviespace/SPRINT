<?php

require_once "utils/session_utils.php";
require_once "db/db_config.php";
require_once "db/Database.php";
require_once "utils/utils.php";

session_start();
check_session();


$database = new Database();
$user = $database->select("SELECT id, name, email FROM `user` WHERE id = ?", [ "i", [$_SESSION['userid']]]);
$pass_errors = [ "new_pass_old_pass_same" => false,
				 "pass_confirm_wrong" => false,
				 "old_pass_incorrect" => false,
				 "pass_updated" => false ]; 

$new_pass_old_pass_same = false;
$pass_confirm_wrong = false;
$old_pass_incorrect = false;
$pass_updated = false;

if (isset($_POST["update_profile"])) {
	$user = update_profile($database);
}

if (isset($_POST["update_password"])) {
	//list($new_pass_old_pass_same, $pass_confirm_wrong, $old_pass_incorrect, $pass_updated) = update_password($database);
	$pass_errors = update_password($database, $pass_errors);
}

$sidebar_actions = [ [ "label" => "Home", "link" => "index.php" ] ];

$pagetitle = "Profile";
$tpl = "profile.tpl.php";
include "template.php";




function update_profile($database)
{
	$database->execute_non_query("UPDATE `user` SET name = ?, email = ? WHERE id = ?",
								 ["ssi", [ $_POST["user_name"], $_POST["user_email"], $_SESSION["userid"]]]);

	return $database->select("SELECT id, name, email FROM `user` WHERE id = ?", [ "i", [$_SESSION['userid']]]);
}

function update_password($database, $pass_errors)
{
	$new_pass_old_pass_same = false;
	$pass_confirm_wrong = false;

	if (strcmp($_POST["pass_old"], $_POST["pass_first"]) == 0)
		$pass_errors["new_pass_old_pass_same"] = true;

	if (strcmp($_POST["pass_first"], $_POST["pass_second"]) == 1)
		$pass_errors["pass_confirm_wrong"] = true;

	if ($pass_errors["new_pass_old_pass_same"] || $pass_errors["pass_confirm_wrong"])
		return $pass_errors;

	// Verify old password is correct
	$user = $database->select("SELECT id, name, email, hash, hash_type FROM `user` WHERE id = ?", [ "i", [$_SESSION['userid']]]);

	if ($user[0]["hash_type"] != 1) {
		$pass_errors["old_pass_incorrect"] = true;
		return $pass_errors;
	}

	if (!password_verify($_POST["pass_old"], $user[0]["hash"])) {
		$pass_errors["old_pass_incorrect"] = true;
		return $pass_errors;
	}

	// Password verified -> change to new password
	$hash = password_hash($_POST["pass_first"], PASSWORD_ARGON2ID);

	$database->execute_non_query("UPDATE `user` SET hash=? WHERE id = ?", ["si", [ $hash, $_SESSION['userid']]]);


	$pass_errors["pass_updated"] = true;
	return $pass_errors;
}

?>

