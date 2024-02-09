<?php

function get_roles_for_header($database)
{
	return $database->select("SELECT id, name FROM parameterrole WHERE filter IN (0, 1) ORDER BY id", null);
}


function get_parameter_values_for_header($database, $standard_id)
{
	return $database->select("SELECT p.id, concat(p.domain, '/', p.name) as parameter " .
							 "FROM parameter p " .
							 "WHERE idStandard = ? AND p.kind IN (0, 1) " .
							 "ORDER BY p.domain, p.name",
							 ["i", [$standard_id]]);
}


function get_parameter_values($database, $standard_id)
{
	return $database->select("SELECT p.id, concat(p.domain, '/', p.name) as parameter " .
							 "FROM parameter p " .
							 "WHERE idStandard = ? ".
							 "ORDER BY p.domain, p.name",
							 ["i", [$standard_id]]);
}

function get_roles($database, $filter)
{
	return $database->select("SELECT id, name FROM parameterrole WHERE filter IN (?, 0) ORDER BY id", ["i", [$filter]]);
}


?>
