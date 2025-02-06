<?php
session_start();

require 'db/db_config.php';
require 'db/Database.php';

$pagetitle = "Login";

// TODO: md5 should not be used anymore
function password_verify_md5($pwd, $hash) {
    if (md5($pwd) == $hash) {
        return true;
    } else {
        return false;
    }
}

if (isset($_GET['login'])) {
	$email = $_POST['email'];
	$password = $_POST['password'];

	$database = new Database();
	$user = $database->select("SELECT id, name, email, password, hash, hash_type FROM `user` WHERE email = ?", ["s", [$email]]);
	if (count($user) == 1) {
		if (   ($user[0]['hash_type'] == 0 && password_verify_md5($password, $user[0]['password']))
			|| ($user[0]['hash_type'] == 1 && password_verify($password, $user[0]['hash'])) ) {

			$_SESSION['userid'] = $user[0]['id'];
			$_SESSION['username'] = $user[0]['name'];
			$_SESSION['email'] = $user[0]['email'];

			// TODO: fix that with proper admin users
			$_SESSION['is_admin'] = $user[0]['id'] == 1 || $user[0]['id'] == 1001;

			$date = date('Y-m-d G:i:s');
			$database->execute_non_query("UPDATE `user` SET lastSignedIn= ? WHERE id = ?", ["si", [$date, $user[0]['id']]]);

			// Change md5 password to argon
			if ($user[0]['hash_type'] == 0) {
				$hash = password_hash($password, PASSWORD_ARGON2ID);
				$database->execute_non_query("UPDATE `user` SET hash_type=1, password=NULL, hash=? WHERE id = ?", ["si", [$hash, $user[0]['id']]]);
			}

			header("refresh:0;url=index.php");
		} else {
			$error_msg = "The email or password is invalid";
		}
	} else {
		$error_msg = "The email or password is invalid";
	}
}

$sidebar_actions = array( array( "link" => "register.php", "label" => "Register"  ) );
$tpl = "login.tpl.php";
include "template.php";

?>
