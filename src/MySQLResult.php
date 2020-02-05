<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

class MySQLResult implements Result {
	/**
	 * The underlying result object
	 *
	 * @var \React\MySQL\QueryResult
	 */
	protected $result;

	public function __construct(\React\MySQL\QueryResult $result) {
		$this->result = $result;
	}

	public function getInsertID(): ?int {
		return $this->result->insertId;
	}

	public function getAffectedRows(): ?int {
		return $this->result->changed;
	}

	public function getColumns(): ?array {
		return array_map(
			function($coldef) {
				return $coldef['name'];
			},
			$this->result->resultFields
		);
	}

	/**
	 * Typecast the result for a single row to what the database reports as type
	 *
	 * @param array $row An untyped database row
	 * @return array The typed database row
	 */
	protected function classifyRow(array $row): array {
		$result = [];
		$i = 0;
		foreach ($row as $col => $value) {
			if (is_string($value) && in_array($this->result->resultFields[$i]['type'], [1, 2, 3, 8, 9])) {
				$result[$col] = (int)$value;
			} else {
				$result[$col] = $value;
			}
			$i++;
		}
		return $result;
	}

	/**
	 * typecast all result rows into proper PHP types
	 *
	 * @param array|null $rows The unclassified result rows
	 * @return array|null
	 */
	protected function classifyRows(?array $rows): ?array {
		if ($rows === null) {
			return null;
		}
		return array_map([$this, 'classifyRow'], $rows);
	}

	public function getRows(): ?array {
		return json_decode(
			json_encode(
				$this->classifyRows($this->result->resultRows)
			)
		);
	}
}