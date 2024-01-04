<?php

interface CrudController
{
	public function get_items($project_id);
	public function get_item($project_id, $id);
	public function create_item($project_id, $item);
	public function delete_item($project_id, $item_id);
	public function put_item($project_id, $item);
}
?>
