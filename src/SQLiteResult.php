<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

class SQLiteResult implements Result {
	/**
	 * The underlying result object
	 *
	 * @var \Clue\React\SQLite\Result
	 */
	protected $result;

	public function __construct(\Clue\React\SQLite\Result $result) {
		$this->result = $result;
	}
	public function getInsertID(): ?int {
		return $this->result->insertId;
	}

	public function getAffectedRows(): ?int {
		return $this->result->changed;
	}

	public function getColumns(): ?array {
		return $this->result->columns;
	}

	public function getRows(): ?array {
		return json_decode(json_encode($this->result->rows));
	}
}