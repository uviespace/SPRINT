<?php

interface CrudController
{
	public function get_items($route_ids);
	public function get_item($route_ids, $id);
	public function create_item($route_ids, $item);
	public function delete_item($route_ids, $item_id);
	public function put_item($route_ids, $item);
}
?>
