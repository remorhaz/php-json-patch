# PHP JSON Patch

[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-patch/v/stable)](https://packagist.org/packages/remorhaz/php-json-patch)
[![Build Status](https://travis-ci.org/remorhaz/php-json-patch.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-patch)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-patch/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-patch)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/remorhaz/php-json-patch/master)](https://infection.github.io)
[![Total Downloads](https://poser.pugx.org/remorhaz/php-json-patch/downloads)](https://packagist.org/packages/remorhaz/php-json-patch)
[![License](https://poser.pugx.org/remorhaz/php-json-patch/license)](https://packagist.org/packages/remorhaz/php-json-patch)

This library implements [RFC6902](https://tools.ietf.org/html/rfc6902)-compliant JSON patch tool.

## Requirements
- PHP 7.3+
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
