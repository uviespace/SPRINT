<?php

interface CrudController
{
	public function get_items(array $route_ids): void;
	public function get_item(array $route_ids, int $id): void;
	public function create_item(array $route_ids, object $item): void;
	public function delete_item(array $route_ids, int $item_id): void;
	public function put_item(array $route_ids, object $item): void;
}
?>
