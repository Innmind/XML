# XML

[![Build Status](https://github.com/innmind/xml/workflows/CI/badge.svg?branch=master)](https://github.com/innmind/xml/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/innmind/xml/branch/develop/graph/badge.svg)](https://codecov.io/gh/innmind/xml)
[![Type Coverage](https://shepherd.dev/github/innmind/xml/coverage.svg)](https://shepherd.dev/github/innmind/xml)

This library is an abstraction on top of the `\DOM*` classes of php, the goal is to clarify the interfaces of each node.

The big differences are that each node is immutable and is only aware of its children (instead of being aware of its parent and siblings). This can allow you to extract a whole subtree and use it to build a new tree without affecting the original one.

**Important**: you must use [`vimeo/psalm`](https://packagist.org/packages/vimeo/psalm) to make sure you use this library correctly.

## Installation

```sh
composer require innmind/xml
```

## Usage

```php
use Innmind\Xml\{
    Reader\Reader,
    Node,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\Maybe;

$read = Reader::of();

$tree = $read(
    Content\Lines::ofContent('<root><foo some="attribute"/></root>')
); // Maybe<Node>
```

## Extract informations out of a node

The library use the visitor pattern to give access back to the raw xml library. For example you can access the parent of a node like this:

```php
use Innmind\Xml\Visitor\ParentNode;

$parent = ParentNode::of($childNode)($treeToSearchIn); // Maybe<Node>
```

Here is the full list of visitors you have access to by default:

* [`NextSibling`](src/Visitor/NextSibling.php)
* [`PreviousSibling`](src/Visitor/PreviousSibling.php)
* [`ParentNode`](src/Visitor/ParentNode.php)
* [`Text`](src/Visitor/Text.php)
