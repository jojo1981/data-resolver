Generic extensible data resolver 
=====================

[![Build Status](https://travis-ci.com/jojo1981/data-resolver.svg?branch=master)](https://travis-ci.com/jojo1981/data-resolver)
[![Coverage Status](https://coveralls.io/repos/github/jojo1981/data-resolver/badge.svg)](https://coveralls.io/github/jojo1981/data-resolver)
[![Latest Stable Version](https://poser.pugx.org/jojo1981/data-resolver/v/stable)](https://packagist.org/packages/jojo1981/data-resolver)
[![Total Downloads](https://poser.pugx.org/jojo1981/data-resolver/downloads)](https://packagist.org/packages/jojo1981/data-resolver)
[![License](https://poser.pugx.org/jojo1981/data-resolver/license)](https://packagist.org/packages/jojo1981/data-resolver)

Author: Joost Nijhuis <[jnijhuis81@gmail.com](mailto:jnijhuis81@gmail.com)>

The data resolver is a resolver which will be declarative created and used to extract data in steps from a tree structure.  
The data resolver will perform all extract operations linear and in order.  
The next operation will be performed on the last result etc...  
Some operations are to extract data from an `object` and others for extracting data of a `sequence`.  
This library has factory class to get a resolver builder and start building a resolver.  
Also custom `comparator`, `merge`, `property` and `sequence` handlers can be registered.

The extract operations are:

- get the next value with `get` using a single property
- get the next value with `get` using multiple properties (The data resolver tries to merge the result)
- get the next value with a `find`, `filter` or `flatten` using a predicate.
- get the next value (boolean) with `all`, `some`, `none` for sequences and `hasProperty` for objects
- get the next value (integer) with `count` for sequences and or `strlen` for strings
- get the next value (float) with `sum` for sequences of integers/floats
- get the next value (mixed) with `callback` for mixed data

The predicates are:

- equals($referenceValue)
- notEquals($referenceValue)
- greaterThan($referenceValue)
- greaterThanOrEquals($referenceValue)
- lessThan($referenceValue)
- lessThanOrEquals($referenceValue)
- isTrue()
- isFalse()
- isTruly()
- isFalsely()
- isNull()
- isNotNull()
- callback(callable $callback)
- not(PredicateBuilderInterface $predicateBuilder)
- some(PredicateBuilderInterface $predicateBuilder)
- all(PredicateBuilderInterface $predicateBuilder)
- none(PredicateBuilderInterface $predicateBuilder)
- in(array $expectedValues)
- notIn(array $expectedValues)
- isEmpty()
- isNotEmpty()
- hasCount(int $expectedCount)
- hasNotCount(int $expectedCount)
- stringStartsWith(string $prefix, bool $caseSensitive = true)
- stringEndsWith(string $suffix, bool $caseSensitive = true)
- stringEndsWith(string $suffix, bool $caseSensitive = true)
- stringContains(string $subString, bool $caseSensitive = true)
- stringMatchesRegex(string $pattern)
- hasProperty(string $propertyName)

The flow:

- Create 1 generic factory instance and add optionally customizations.
- Get 1 resolver builder factory with `getResolverBuilderFactory`  
(The generic factory will be frozen and can not be customized anymore, this way the generic factory will always produced a resolver builder factory provided with the same setup)
- Get for every to build resolver a fresh resolver builder from the resolver builder factory with `create`, `compose`, `get`, `filter`, `flatten`, `find`, `all`, `none`, `some`, `count` or `strlen`
- The resolver builder must be build to get a resolver. This resolver is immutable and can only be used to resolver data from a tree structure.
- Use the resolver with the `resolve` method and give it some data

To create a predicate builder call `or`, `and`, `not` or `where` on the resolver builder.

Setup generic factory instance:

- Invoke `registerPropertyHandler` to register a custom property handler
- Invoke `registerSequenceHandler` to register a custom sequence handler
- Invoke `useDefaultPropertyHandlers` when you have custom property handlers and register them with `registerPropertyHandler`.  
This is not needed when you do not have custom property handlers registered.    
You can invoke this method before or after the registration of the custom property handlers in order to determine the priority of the handlers  
When this method is *NOT* invoked and there are custom property handlers registered the default property handlers are *NOT* registered
- Invoke `useDefaultSequenceHandlers` when you have custom sequence handlers and register them with `registerSequenceHandler`.  
This is not needed when you do not have custom sequence handlers registered.    
You can invoke this method before or after the registration of the sequence property handlers in order to determine the priority of the handlers  
When this method is *NOT* invoked and there are custom sequence handlers registered the default sequence handlers are *NOT* registered
- Invoke `setMergeHandler` to inject a custom merge handler (replaces the default)
- Invoke `setNamingStrategy` to inject a custom naming strategy (replaces the default)
- Invoke `setComparator` to inject a custom comparator (replaces the default)

Get resolver builder factory from generic factory:

- Invoke `getResolverBuilderFactory` to get the resolver builder factory which can be used to create multiple resolver builders

Create customizations:

- A property handler is a class which implement interface: `\Jojo1981\DataResolver\Handler\PropertyHandlerInterface`
- A sequence handler is a class which implement interface: `\Jojo1981\DataResolver\Handler\SequenceHandlerInterface`
- A merge handler is a class which implement interface: `\Jojo1981\DataResolver\Handler\MergeHandlerInterface`
- A naming strategy is a class which implement interface: `\Jojo1981\DataResolver\NamingStrategy\NamingStrategyInterface`
- A comparator is a class which implement interface: `\Jojo1981\DataResolver\Comparator\ComparatorInterface`

## Installation

### Library

```bash
git clone https://github.com/jojo1981/data-resolver.git
```

### Composer

[Install PHP Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require jojo1981/data-resolver
```

## Basic usage

A simple example how to use the resolver.  
More complex examples will be added here to the documentation in the future. 

```php
<?php

require 'vendor/autoload.php';

$testData = [
  'data' => [
    ['name' => 'John', 'age' => 40, 'gender' => 'M'],
    ['name' => 'Jane', 'age' => 35, 'gender' => 'F'],
    ['name' => 'Rachel', 'age' => 15, 'gender' => 'F'],
    ['name' => 'Dennis', 'age' => 12, 'gender' => 'M'],
    ['name' => 'Bob', 'age' => 6, 'gender' => 'M']
  ]
];

$genericFactory = new \Jojo1981\DataResolver\Factory();
$resolverBuilderFactory = $genericFactory->getResolverBuilderFactory();

$dataResolverBuilder = $resolverBuilderFactory->get('data');
$agePredicateBuilder1 = $resolverBuilderFactory->where('age')->greaterThanOrEquals(35);
$agePredicateBuilder2 = $resolverBuilderFactory->where('age')->greaterThanOrEquals(45);

// will be all persons
\print_r($dataResolverBuilder->build()->resolve($testData));

// Will be 5
\var_dump($dataResolverBuilder->count()->resolve($testData));

// filtered data contains John and Jane
\print_r($dataResolverBuilder->filter($agePredicateBuilder1)->build()->resolve($testData));

// empty array
\print_r($dataResolverBuilder->filter($agePredicateBuilder2)->build()->resolve($testData));

// Will be true
\var_dump($dataResolverBuilder->some($agePredicateBuilder1)->resolve($testData));

// Will be false
\var_dump($dataResolverBuilder->all($agePredicateBuilder1)->resolve($testData));

// Will be false
\var_dump($dataResolverBuilder->none($agePredicateBuilder1)->resolve($testData));

// Will be 3
\var_dump(
  $resolverBuilderFactory
      ->find($resolverBuilderFactory->where('age')->equals(6))
      ->get('name')
      ->strlen()
      ->resolve($testData['data'])
);

// more examples to come...

```