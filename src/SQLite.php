<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

/**
 * Maps the \Clue\React\SQLite database connections interface to \Nadyita\ReactDB\SQLDB
 */
class SQLite implements SQLDB {
	/**
	 * The actual database connection
	 *
	 * @var \Clue\React\SQLite\DatabaseInterface
	 */
	protected $connection;

	public function __construct(\Clue\React\SQLite\DatabaseInterface $connection) {
		$this->connection = $connection;
	}

	public function remapQueryPromise(\Clue\React\SQLite\Result $result) {
		return new SQLiteResult($result);
	}

	public function query(string $query, array $params=[]): \React\Promise\PromiseInterface {
		return $this->connection->query(...func_get_args())->then([$this, 'remapQueryPromise']);
	}

	public function exec(string $query): \React\Promise\PromiseInterface {
		return $this->connection->exec(...func_get_args())->then([$this, 'remapQueryPromise']);
	}

	public function quit(): \React\Promise\PromiseInterface {
		return $this->connection->quit();
	}

	public function close(): void {
		$this->connection->close();
	}
}
