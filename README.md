# PHP JSON Patch

[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-patch/v/stable)](https://packagist.org/packages/remorhaz/php-json-patch)
[![License](https://poser.pugx.org/remorhaz/php-json-patch/license)](https://packagist.org/packages/remorhaz/php-json-patch)
[![Build Status](https://travis-ci.org/remorhaz/php-json-patch.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-patch)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-patch/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-patch/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-patch)

This library implements [RFC6902](https://tools.ietf.org/html/rfc6902)-compliant JSON patch tool.

## Requirements
* PHP 7.1+

## Features
* Supports PHP 7.1
* No PHP extensions required
* Throws SPL exceptions

# License
PHP JSON Patch is licensed under MIT license.

# Installation
You will need [composer](https://getcomposer.org) to perform install.
```
composer require remorhaz/php-json-patch
```

# Documentation
## Data accessors
Patch tool utilizes JSON data accessor interfaces defined in package
**[remorhaz/php-json-data](https://github.com/remorhaz/php-json-data)**. Read more about them in package documentation.
There is a ready-to-work implementation in that package that works with native PHP structures (like the ones you get as
a result of `json_decode` function). You can use `Remorhaz\JSON\Data\Reference\Selector` class to bind to patch data and
`Remorhaz\JSON\Data\Reference\Writer` class to bind to the document that is to be patched. You can also implement your own accessors
if you need to work with another sort of data (like unparsed JSON text, for example).

## Using patch tool
To apply JSON Patch to the JSON document you need just 4 simple steps:

1. Create an instance of read-only accessor bound to your patch data.
2. Create an instance of writabe accessor bound to your document.
3. Create an object of `\Remorhaz\JSON\Patch\Patch` by calling it's constructor with a document accessor as an argument.
4. Call its `apply()` method with patch accessor as an argument.

## Example of usage
```php
<?php

use Remorhaz\JSON\Data\Reference\Selector;
use Remorhaz\JSON\Data\Reference\Writer;
use Remorhaz\JSON\Patch\Patch;

// Setting up document.
$data = (object) ['a' => (object) ['b' => 'c', 'd' => 'e']];
$dataWriter = new Writer($data);

// Setting up patch.
$patchData = [
    (object) ['op' => 'add', 'path' => '/a/f', 'value' => 'g'],
];
$patchSelector = new Selector($patchData);

// Applying the patch.
(new Patch($dataWriter))->apply($patchSelector); // $data->a->f property is added and set to 'g'
