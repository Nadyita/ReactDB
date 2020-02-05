<?php

declare(strict_types=1);

namespace Nadyita\ReactDB\Tests;

use Nadyita\ReactDB\Tests\Base\TestCase;

final class testMySQL extends TestCase {
	public function getDBMock(): \PHPUnit\Framework\MockObject\MockObject {
		return $this->createMock(\React\MySQL\ConnectionInterface::class);
	}

	/**
	 * @small
	 */
	public function testCanBeInstantiated(): void {
		$dbMock = $this->getDBMock();
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$db = new \Nadyita\ReactDB\MySQL($dbMockFinal);
		$this->assertInstanceOf(\Nadyita\ReactDB\MySQL::class, $db);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testCanQuitConnection(): void {
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('quit')
			->willReturn(\React\Promise\resolve(true));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
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
	public function testPassesOnQuitErrors(): void {
		$expectedException = new \RuntimeException('whatever');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('quit')
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
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
	public function testCanCloseConnection(): void {
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('close');
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
			->close();
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleSelectQuery(): void {
		$dummyResult = new \React\MySQL\QueryResult();
		$dummyResult->resultFields = [
			['name' => 'id', 'type' => 3],
			['name' => 'foo', 'type' => 253]
		];
		$dummyResult->resultRows = [
			['id' => '1', 'foo' => null],
			['id' => '2', 'foo' => 'Bar'],
		];
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('SELECT * FROM foo WHERE bar=?', [1])
			->willReturn(\React\Promise\resolve($dummyResult));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
			->query('SELECT * FROM foo WHERE bar=?', [1])
			->then(
				function(\Nadyita\ReactDB\Result $result) use (&$data) {
					$data = $result;
				},
				$this->expectCallableNever()
			);

		$this->assertSame(1, $data->getRows()[0]->id);
		$this->assertSame(2, $data->getRows()[1]->id);
		$this->assertSame(null, $data->getRows()[0]->foo);
		$this->assertSame('Bar', $data->getRows()[1]->foo);
	}

	/**
	 * @depends testCanBeInstantiated
	 * @small
	 */
	public function testSimpleSelectQueryError(): void {
		$expectedException = new \RuntimeException('foo');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('SELECT * FROM foo WHERE bar=?', [1])
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
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
	public function testSimpleExecResolvesInResultWithoutRows(): void {
		$dummyResult = new \React\MySQL\QueryResult();
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('CREATE TABLE foo(INT bar)')
			->willReturn(\React\Promise\resolve($dummyResult));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$data = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
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
	public function testSimpleExecErrorResolvesIntoOtherwise(): void {
		$expectedException = new \RuntimeException('foo');
		$dbMock = $this->getDBMock();
		$dbMock->expects($this->once())
			->method('query')
			->with('CREATE TABLE foo(INT bar)')
			->willReturn(\React\Promise\reject($expectedException));
		/** @var \React\MySQL\ConnectionInterface */ $dbMockFinal = $dbMock;
		$thrownException = null;
		(new \Nadyita\ReactDB\MySQL($dbMockFinal))
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
