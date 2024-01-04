<?php

require_once "BaseController.php";
require_once "CrudController.php";
require_once "../../db/Database.php";

class ProjectController extends BaseController {

	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	public function get_project($id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM project WHERE id = ?", ["i", [$id]]);
		$this->send_output(json_encode($data));
	}
}


class StandardsController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($project_id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM standard WHERE idProject = ?", ["i", [$project_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	public function create_item($project_id, $item)
	{
		$id = $this->database->insert("INSERT INTO standard(idProject, name, `desc`) VALUES(?, ?, ?)",
									  ["iss", [$item->idProject, $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($project_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM standard WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($project_id, $item)
	{
		$this->database->execute_non_query("UPDATE standard SET name = ?, `desc` = ? WHERE id = ?",
										   ["ssi", [$item->name, $item->desc, $item->id ]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class ApplicationController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($project_id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM application WHERE idProject = ?", ["i", [$project_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	public function create_item($project_id, $item)
	{
		$id = $this->database->insert("INSERT INTO application(idProject, name, `desc`) VALUES(?,?,?)",
									  ["iss", [$item->idProject, $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($project_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM application WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($project_id, $item)
	{
		$this->database->execute_non_query("UPDATE application SET name = ?, `desc` = ? WHERE id = ?",
										   ["ssi", [$item->name, $item->desc, $item->id ]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

?>
