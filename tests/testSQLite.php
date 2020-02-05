<?php

declare(strict_types=1);

namespace Nadyita\ReactDB\Tests;

use Nadyita\ReactDB\Tests\Base\TestCase;

final class testSQLite extends TestCase {

	public function getDBMock(): \PHPUnit\Framework\MockObject\MockObject {
		return $this->createMock(\Clue\React\SQLite\DatabaseInterface::class);
	}

	/**
	 * @small
	 */
	public function testCanBeInstantiated() {
		$dbMock = $this->getDBMock();
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$db = new \Nadyita\ReactDB\SQLite($dbMockFinal);
		$this->assertInstanceOf(\Nadyita\ReactDB\SQLite::class, $db);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testCanQuitConnection() {
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('quit')
			->willReturn(\React\Promise\resolve(true));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->quit()
			->then(
				function($result) use(&$data) {
					$data = $result;
				},
				$this->expectCallableNever(),
			);
		$this->assertSame(true, $data);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testPassesOnQuitErrors() {
		$expectedException = new \RuntimeException('whatever');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('quit')
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->quit()
			->then(
				$this->expectCallableNever(),
				function(\Throwable $e) use (&$thrownException) {
					$thrownException = $e;
				}
			);
		$this->assertSame($expectedException, $thrownException);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testCanCloseConnection() {
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('close');
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->close();
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleSelectQuery() {
		$dummyResult = new \Clue\React\SQLite\Result();
		$dummyResult->columns = ['id', 'foo'];
		$dummyResult->rows = [
			['id' => 1, 'foo' => null],
			['id' => 2, 'foo' => 'Bar'],
		];
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('SELECT * FROM foo WHERE bar=?', [1])
			->willReturn(\React\Promise\resolve($dummyResult));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->query('SELECT * FROM foo WHERE bar=?', [1])
			->then(
				function(\Nadyita\ReactDB\Result $result) use (&$data) {
					$data = $result;
				},
				$this->expectCallableNever()
			);

		$this->assertSame($dummyResult->rows[0]['id'], $data->getRows()[0]->id);
		$this->assertSame($dummyResult->rows[1]['id'], $data->getRows()[1]->id);
		$this->assertSame($dummyResult->rows[0]['foo'], $data->getRows()[0]->foo);
		$this->assertSame($dummyResult->rows[1]['foo'], $data->getRows()[1]->foo);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleSelectQueryError() {
		$expectedException = new \RuntimeException('foo');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('SELECT * FROM foo WHERE bar=?', [1])
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->query('SELECT * FROM foo WHERE bar=?', [1])
			->then(
				$this->expectCallableNever(),
				function(\Throwable $e) use(&$thrownException) {
					$thrownException = $e;
				}
			);

		$this->assertSame($expectedException, $thrownException);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleExecResolvesInResultWithoutRows() {
		$dummyResult = new \Clue\React\SQLite\Result();
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('exec')
			->with('CREATE TABLE foo(INT bar)')
			->willReturn(\React\Promise\resolve($dummyResult));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->exec('CREATE TABLE foo(INT bar)')
			->then(
				function(\Nadyita\ReactDB\Result $result) use (&$data) {
					$data = $result;
				},
				$this->expectCallableNever()
			);

		$this->assertNull($data->getRows());
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleExecErrorResolvesIntoOtherwise() {
		$expectedException = new \RuntimeException('foo');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('exec')
			->with('CREATE TABLE foo(INT bar)')
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \Clue\React\SQLite\DatabaseInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\SQLite($dbMockFinal))
			->exec('CREATE TABLE foo(INT bar)')
			->then(
				$this->expectCallableNever(),
				function (\Throwable $e) use (&$thrownException) {
					$thrownException = $e;
				},
			);

		$this->assertSame($expectedException, $thrownException);
	}
}