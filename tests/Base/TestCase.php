<?php

declare(strict_types=1);

namespace Nadyita\ReactDB\Tests\Base;

class TestCase extends \PHPUnit\Framework\TestCase {
	public function expectCallableNever(): callable {
		$mock = $this->createCallableMock();
		$mock
			->expects($this->never())
			->method('__invoke');
		return $mock;
	}

	public function createCallableMock(): callable {
		$mock = $this
			->getMockBuilder(\Nadyita\ReactDB\Tests\Stub\CallableStub::class)
			->getMock();
		/** @var callable */ $finalMock = $mock;
		return $finalMock;
	}
}
