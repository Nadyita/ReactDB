<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

/**
 * Maps the \React\MySQL database connection interface to \Nadyita\ReactDB\SQLDB
 */
class MySQL implements SQLDB {
	/**
	 * The actual database connection
	 *
	 * @var \React\MySQL\ConnectionInterface
	 */
	protected $connection;

	public function __construct(\React\MySQL\ConnectionInterface $connection) {
		$this->connection = $connection;
	}

	public function remapQueryPromise(\React\MySQL\QueryResult $result) {
		return new MySQLResult($result);
	}

	public function query(string $query, array $params=[]): \React\Promise\PromiseInterface {
		return $this->connection->query(...func_get_args())->then([$this, 'remapQueryPromise']);
	}

	public function exec(string $query): \React\Promise\PromiseInterface {
		return $this->connection->query($query)->then([$this, 'remapQueryPromise']);
	}

	public function quit(): \React\Promise\PromiseInterface {
		return $this->connection->quit();
	}

	public function close(): void {
		$this->connection->close();
	}
}
