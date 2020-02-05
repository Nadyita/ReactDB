<?php

declare(strict_types=1);

namespace Nadyita\ReactDB\Tests\Stub;

/**
 * A dummy class that defines an __invoke() method so it can be stubbed/mocked
 */
class CallableStub {
	public function __invoke() { }
}
