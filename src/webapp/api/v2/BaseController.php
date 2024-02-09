<?php

class BaseController
{
	/**
	 * __call magic method
	 */

	public function __call($name, $arguments)
	{
		$this->send_output('', array('HTTP/1.1 404 Not Found'));
	}


	public function forbidden()
	{
		$this->send_output('', array('HTTP/1.1 403 Forbidden'));
	}

	public function send_error($error)
	{
		$this->send_output($error, array('HTTP/1.1 500 Internal Server Error'));
	}
	

	/**
	 * Get URI elements
	 *
	 * @return array
	 */
	protected function get_uri_segments()
	{
		$uri = parse_url($_SERVER['REQUEST_URI']. PHP_URL_PATH);
		$uri = explode('/', $uri);

		return $uri;
	}


	/**
	 * Get querystring params
	 *
	 * @return array
	 */
	protected function getQueryStringParams()
	{
		return parse_str($_SERVER['QUERY_STRING'], $query);
	}


	/**
	 * send API output
	 *
	 * @param mixed $data
	 * @param string $http_header
	 */
	public function send_output($data, $http_headers=array())
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
