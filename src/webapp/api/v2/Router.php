<?php

require_once "BaseController.php";

class Node
{
	public $node_name;
	public $is_id_node;
	public $method;
	public $children;

	public function __construct($node_name)
	{
		$this->node_name = $node_name;
		$this->children = array();
		$this->method = NULL;

		if ($node_name[0] == ':')
			$this->is_id_node = true;
		else
			$this->is_id_node = false;
	}

	public function has_id_node()
	{
		foreach($this->children as $key => $value) {
			if ($key[0] == ':')
				return true;
		}

		return false;
	}
		
	public function get_id_node()
	{
		foreach($this->children as $key => $value) {
			if ($key[0] == ':')
				return array($key, $value);
		}

		return array();
	}
}


class Router
{
	private $supportedHttpMethods = array("GET", "POST", "PUT", "DELETE");

	private $baseController;
	private $get;
	private $post;
	private $put;
	private $delete;


	function __construct()
	{
		$this->baseController = new BaseController();

		$this->get = new Node("root");
		$this->post = new Node("root");
		$this->put = new Node("root");
		$this->delete = new Node("root");
	}
	

	// call like this:
	// $router->get("api/get_something", function() { });
	function __call($name, $args)
	{
		list($route, $method) = $args;
		//echo $route;
		if (!in_array(strtoupper($name), $this->supportedHttpMethods))
			$this->invalidMethodHandler();

		$route_parts = explode("/", $route);
		$method_node = $this->{strtolower($name)};

		for($i = 0; $i < count($route_parts); $i++) {
			if (array_key_exists($route_parts[$i], $method_node->children)) {
				$method_node = $method_node->children[$route_parts[$i]];
			} else {
				$method_node->children += [$route_parts[$i] => new Node($route_parts[$i])];
				$method_node = $method_node->children[$route_parts[$i]];
			}
		}

		$method_node->method = $method;
	}

	function resolve($uri, $start_routing_with = 2)
	{
		//echo($uri);
		$uri = explode('/', $uri);
		//print_r($uri);
		//print_r($this->delete->children);

		// get root node for request method
		$method_node = $this->{strtolower($_SERVER['REQUEST_METHOD'])};
		$route_ids = [];

		for ($i = $start_routing_with; $i < count($uri); $i++) {
			//echo "Index: " . $i . "\n";
			//print_r($method_node->children);
			if (array_key_exists($uri[$i], $method_node->children)) {
				//echo "Node: " . $method_node->node_name . "\n";
				$method_node = $method_node->children[$uri[$i]];
			} else if (is_numeric($uri[$i]) and $method_node->has_id_node()) {
				list($key, $value) = $method_node->get_id_node();

				// ids are always numeric 
				if (!is_numeric($uri[$i]))
					$this->defaultRequestHandler();

				//echo $value->node_name;

				$route_ids += [ substr($key, 1) => $uri[$i]];
				$method_node = $value;
			} else {
				//echo "Method not found";
				$this->defaultRequestHandler();
				return;
			}
		}

		if ($method_node->method != NULL) {
			//echo "What are we even calling";
			//echo $method_node;
			call_user_func($method_node->method, $route_ids);
		} else
			$this->defaultRequestHandler();
	}
	

	private function invalidMethodHandler()
	{
		$this->baseController->send_output("", array("HTTP/1.1 405 Method not allowed"));
		exit;
	}

	private function defaultRequestHandler()
	{
		$this->baseController->send_output("", array("HTTP/1.1 404 Not Found"));
		exit;
	}
}


?>
