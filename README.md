# PHP JSON Patch

[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-patch/v/stable)](https://packagist.org/packages/remorhaz/php-json-patch)
[![Build](https://github.com/remorhaz/php-json-patch/actions/workflows/build.yml/badge.svg)](https://github.com/remorhaz/php-json-patch/actions/workflows/build.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-patch/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-patch)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fremorhaz%2Fphp-json-patch%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/remorhaz/php-json-patch/master)[![Total Downloads](https://poser.pugx.org/remorhaz/php-json-patch/downloads)](https://packagist.org/packages/remorhaz/php-json-patch)
[![License](https://poser.pugx.org/remorhaz/php-json-patch/license)](https://packagist.org/packages/remorhaz/php-json-patch)

This library implements [RFC6902](https://tools.ietf.org/html/rfc6902)-compliant JSON patch tool.

## Requirements
- PHP 8.1.
- [JSON extension](https://www.php.net/manual/en/book.json.php) (ext-json) - required by [remorhaz/php-json-data](https://github.com/remorhaz/php-json-data) to access JSON documents.
- [Internationalization functions](https://www.php.net/manual/en/book.intl.php) (ext-intl) - required by [`remorhaz/php-json-data`](https://github.com/remorhaz/php-json-data) to compare Unicode strings.

## Installation
You will need [composer](https://getcomposer.org) to perform install.
```
composer require remorhaz/php-json-patch
```

## Documentation
### Accessing JSON document
You can create accessible JSON document either from encoded JSON string or from decoded JSON data using corresponding _node value factory_:
```php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Data\Value\DecodedJson;

// Creating document from JSON-encoded string:
$encodedValueFactory = EncodedJson\NodeValueFactory::create();
$encodedJson = '{"a":1}';
$document1 = $encodedValueFactory->createValue($encodedJson);

// Creating document from decoded JSON data:
$decodedValueFactory = DecodedJson\NodeValueFactory::create();
$decodedJson = (object) ['a' => 1];
$document2 = $decodedValueFactory->createValue($decodedJson);
```

### Creating and processing query
You should use _query factory_ to create query from JSON Patch document. Then you should use _processor_ to apply that query:
```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Patch\Processor\Processor;
use Remorhaz\JSON\Patch\Query\QueryFactory;

$encodedValueFactory = EncodedJson\NodeValueFactory::create();
$queryFactory = QueryFactory::create();
$processor = Processor::create();

$patch = $encodedValueFactory->createValue('[{"op":"remove","path":"/0"}]');
$query = $queryFactory->createQuery($patch);

$document = $encodedValueFactory->createValue('[1,2]');
$result = $processor->apply($query, $document);

var_dump($result->encode()); // string: '[2]'
var_dump($result->decode()); // array: [2]
```
Note that result can be exported either to JSON-encoded string or to raw PHP value.

## License
PHP JSON Patch is licensed under [MIT license](./LICENSE).
