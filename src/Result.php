<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

/**
 * A wrapper to access database operation results
 */
interface Result {
	/**
	 * Get the ID of the last inserted auto increment (if any)
	 *
	 * @return integer|null
	 */
	public function getInsertID(): ?int;

	/**
	 * Get how many rows were affected by the last insert/update/delete query
	 *
	 * @return integer|null
	 */
	public function getAffectedRows(): ?int;

	/**
	 * Get a list of column names of the result
	 *
	 * @return string[]|null
	 */
	public function getColumns(): ?array;

	/**
	 * Get an array with result objects or null on error/non-select query
	 *
	 * @return \StdClass[]|null
	 */
	public function getRows(): ?array;
}