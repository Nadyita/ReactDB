# React-based Database abstraction

A wrapper around [ReactPHP](https://reactphp.org/)'s MySQL and SQLite implementations
that wraps them in a unified interface, so that you don't have to worry about
which database implementation you are actually talking to.

## Quickstart example

Here is an SQLite database connection using this interface

```php
$loop = React\EventLoop\Factory::create();

$factory = new Clue\React\SQLite\Factory($loop);

$db = new Nadyita\ReactDB\SQLite($factory->openLazy('test.db'));

$db->exec('CREATE TABLE IF NOT EXISTS foo (id INTEGER PRIMARY KEY AUTOINCREMENT, bar STRING)');

$name = 'Alice';
$db->query('INSERT INTO foo (bar) VALUES (?)', [$name])->then(
    function (Nadyita\ReactDB\Result $result) use ($name) {
        echo 'New ID for ' . $name . ': ' . $result->getinsertID() . PHP_EOL;
    }
);

$db->quit();

$loop->run();
```

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project follows [SemVer](https://semver.org/).
This will install the latest supported version:

```bash
$ composer require nadyita/reactdb:^1.0
```

See also the [CHANGELOG](CHANGELOG.md) for details about version upgrades.

This project aims to run on any platform and thus does not require any PHP
extensions and supports running on PHP 7.3+.

## Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ composer test
```

## License

MIT, see [LICENSE file](LICENSE).
