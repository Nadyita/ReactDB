<?php

declare(strict_types=1);

namespace Nadyita\ReactDB;

interface SQLDB {

	/**
	 * The query method can be used to perform an async query.
	 *
	 * This method returns a promise that will resolve with a Result on success
	 * or will reject with an Exception on error.
	 *
	 * @param string $query The actual SQL query with or without ? placeholders
	 * @param array $params The parameters to the placeholders (if any)
	 * @return \React\Promise\PromiseInterface
	 */
	public function query(string $query, array $params=[]): \React\Promise\PromiseInterface;

	/**
	 * The exec method can be used to perform an async query that doesn't return data
	 *
	 * This method returns a promise that will resolve with a Result on success
	 * or will reject with an Exception on error.
	 *
	 * @param string $query The actual SQL query without ? placeholders
	 * @return \React\Promise\PromiseInterface
	 */
	public function exec(string $query): \React\Promise\PromiseInterface;

	/**
	 * quit (soft-close) the connection.
	 *
	 * This method returns a promise that will resolve (with a void value) on success
	 * or will reject with an Exception on error
	 *
	 * @return \React\Promise\PromiseInterface
	 */
	public function quit(): \React\Promise\PromiseInterface;

	/**
	 * close (force-close) the connection
	 *
	 * @return void
	 */
	public function close(): void;
}
