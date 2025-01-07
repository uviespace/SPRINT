<?php

require_once "db_config.php";

class Database
{
	protected $connection = null;

	public function __construct()
	{
		$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		if (mysqli_connect_errno()) {
			throw new Exception("Could not connect to database");
		}
	}

	private function prepare($query = "", $params = [])
	{
		$stmt = $this->connection->prepare($query);

		if ($stmt === false) {
			throw new Exception("Unable to do prepared statement");
		}

		if ($params) {
			$stmt->bind_param($params[0], ...$params[1]);
		}

		$stmt->execute();

		return $stmt;
	}


	public function select($query = "", $params = [])
	{
		$stmt = $this->prepare($query, $params);
		$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$stmt->close();

		return $result;
	}

	public function execute_non_query($query = "", $params = [])
	{
		$stmt = $this->prepare($query, $params);
		$stmt->close();
	}

	public function insert($query = "", $params = [])
	{
		$stmt = $this->prepare($query, $params);
		$id = $this->connection->insert_id;
		$stmt->close();

		return $id;
	}

	public function begin_transaction()
	{
		$this->connection->begin_transaction();
	}

	public function rollback()
	{
		$this->connection->rollback();
	}

	public function commit()
	{
		$this->connection->commit();
	}
	
	
	
}

?>
