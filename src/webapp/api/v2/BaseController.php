<?php

class BaseController
{
	/**
	 * __call magic method
	 *
	 * @param array $arguments arguments of the called function
	 */
	public function __call(string $name, array $arguments): void
	{
		$this->send_output('', array('HTTP/1.1 404 Not Found'));
	}


	public function forbidden(): void
	{
		$this->send_output('', array('HTTP/1.1 403 Forbidden'));
	}

	public function send_error(string $error): void
	{
		$this->send_output($error, array('HTTP/1.1 500 Internal Server Error'));
	}
	

	/**
	 * Get URI elements
	 *
	 * @return array
	 */
	protected function get_uri_segments(): array
	{
		$uri = parse_url($_SERVER['REQUEST_URI']. PHP_URL_PATH);
		$uri = explode('/', $uri);

		return $uri;
	}


	/**
	 * Get querystring params
	 *
	 * @param array $query array where query params are stored
	 */
	protected function getQueryStringParams(array &$query): void 
	{
		parse_str($_SERVER['QUERY_STRING'], $query);
	}


	/**
	 * send API output
	 *
	 * @param mixed $data
	 * @param array $http_headers
	 */
	public function send_output($data, $http_headers=array()): void
	{
		header_remove('Set-Cookie');

		if (is_array($http_headers) && count($http_headers)) {
			foreach ($http_headers as $http_header) {
				header($http_header);
			}
		}

		echo $data;
		exit;
	}
	
}

?>
