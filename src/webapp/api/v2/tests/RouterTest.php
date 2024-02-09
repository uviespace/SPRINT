<?php
use PHPUnit\Framework\TestCase;
require "../Router.php";

final class RouterTest extends TestCase
{
	public function test_resolve(): void
	{
		$router = new Router();

		$router->get("api/v2/projects/:project_id/standards", function($route_ids) {
			$this->assertSame("1016", $route_ids["project_id"]);
		});

		$_SERVER['REQUEST_METHOD'] = "GET";

		$router->resolve("/SPRINT_NEXT/api/v2/projects/1016/standards");
	}


	public function test_resolve_multiple_route_ids(): void
	{
		$router = new Router();

		$router->get("api/v2/projects/:project_id/standards/:standard_id", function($route_ids) {
			$this->assertSame("1016", $route_ids["project_id"]);
			$this->assertSame("1024", $route_ids["standard_id"]);
		});


		$_SERVER['REQUEST_METHOD'] = "GET";

		$router->resolve("/SPRINT_NEXT/api/v2/projects/1016/standards/1024");
	}


	public function test_resolve_multiple_endpoints(): void
	{
		$router = new Router();

		$router->get("api/v2/projects", function($router_ids) {
			$this->assertTrue(false);
		});

		$router->get("api/v2/projects/:project_id", function($route_ids) {
			$this->assertTrue(false);
		});

		$router->get("api/v2/projects/:project_id/standards", function($route_ids) {
			$this->assertTrue(false);
		});

		$router->get("api/v2/projects/:project_id/standards/:standard_id", function($route_ids) {
			$this->assertSame("1016", $route_ids["project_id"]);
			$this->assertSame("1024", $route_ids["standard_id"]);
		});

		$_SERVER['REQUEST_METHOD'] = "GET";

		$router->resolve("/SPRINT_NEXT/api/v2/projects/1016/standards/1024");
	}
	
}


?>
