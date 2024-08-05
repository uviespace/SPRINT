<?php
session_start();

require 'db/db_config.php';
require 'db/Database.php';

$error = [];
$sign_up_success = false;

if(isset($_POST['register'])) {
	$database = new Database();

	
	// check email valid
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		array_push($error, "Please enter a valid email address");

	// check if email already exists
	$email_cnt = $database->select("SELECT count(email) as email_cnt FROM `user` u WHERE email = ?", ["s", [$_POST['email']]]);
	if ($email_cnt[0]["email_cnt"] != 0)
		array_push($error, "E-Mail already exists");

	// check if passwords match
	if ($_POST["pass_1"] != $_POST["pass_2"])
		array_push($error, "Passwords are not the same");

	// check if password longer than 4 characters
	if(strlen($_POST["pass_1"]) < 4)
		array_push($error, "Password too short");

	// check if sign up code is correct
	if ($_POST["code"] != "ARIEL_2029")
		array_push($error, "Sign up code is invalid");
	
	if (count($error) == 0) {
		$hash = password_hash($_POST["pass_1"], PASSWORD_ARGON2ID);
		$date_signed_up = date('Y-m-d G:i:s');

		try {
			$database->insert("INSERT INTO `user`(name, email, signedUp, hash, hash_type) VALUES(?,?,?,?,1)",
							  ["ssss", [$_POST["name"], $_POST["email"], $date_signed_up, $hash] ]);
			$sign_up_success = true;
		} catch(\Throwable $e) {
			array_push($error, "Could not create user: " + $e->getMessage());
		}
	}
}








$sidebar_actions = array( array("label" => "Login", "link" => "login.php" ) );
$pagetitle = "Register";
$tpl = "register.tpl.php";
include "template.php";
?>
