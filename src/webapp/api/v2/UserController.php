<?php

require_once "BaseController.php";
require_once "CrudController.php";
require_once "../../db/Database.php";


class UserController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items(array $route_ids): void
	{
		$data = $this->database->select("SELECT id, name, email FROM `user` ORDER BY id");
		$this->send_output(json_encode($data));
	}

	public function get_item(array $route_ids, int $id): void
	{
		$this->not_found();
	}


	public function create_item(array $route_ids, object $item): void
	{
		$id = $this->database->insert("INSERT INTO user(name, email) VALUES(?,?)",
									  ["ss", [$item->name, $item->email]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item(array $route_ids, int $item_id): void
	{
		$this->database->execute_non_query("DELETE FROM `user` WHERE id = ?", ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item(array $route_ids, object $item): void
	{
		$this->database->execute_non_query("UPDATE `user` SET name = ?, email = ? WHERE id = ?",
										   ["ssi", [$item->name, $item->email, $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

?>
