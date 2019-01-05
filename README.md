# XML

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/XML/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/XML/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/XML/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/XML/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/XML/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/XML/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/XML/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/XML/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/XML/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/XML/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/XML/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/XML/build-status/develop) |

This library is an abstraction on top of the `\DOM*` classes of php, the goal is to clarify the interfaces of each node.

The big differences are that each node is immutable and is only aware of its children (instead of being aware of its parent and siblings). This can allow you to extract a whole subtree and use it to build a new tree without affecting the original one.

## Installation

```sh
composer require innmind/xml
```

## Usage

```php
use function Innmind\Xml\bootstrap;
use Innmind\Filesystem\Stream\Stream;

$read = bootstrap()['reader'];

$tree = $read(
    new StringStream('<root><foo some="attribute"/></root>')
);
```

Here you have `$tree` which is an instance of [`Node`](src/Node.php). In this tree you'll find `root` and `foo` which fulfill [`Element`](src/Element.php).

## Extract informations out of a node

The library use the visitor pattern to give access back to the raw xml library. For example you can access the parent of a node like this:

```php
use Innmind\Xml\Visitor\ParentNode;

$parent = (new ParentNode($childNode))($treeToSearchIn);
```

Here `$parent` will always be an instance of `Node`, in case the parent is not found an exception will be thrown.

Here is the full list of visitors you have access to by default:

* [`FirstChild`](src/Visitor/FirstChild.php)
* [`LastChild`](src/Visitor/LastChild.php)
* [`NextSibling`](src/Visitor/NextSibling.php)
* [`NextSibling`](src/Visitor/NextSibling.php)
* [`PreviousSibling`](src/Visitor/PreviousSibling.php)
* [`PreviousSibling`](src/Visitor/PreviousSibling.php)
* [`ParentNode`](src/Visitor/ParentNode.php)
* [`ParentNode`](src/Visitor/ParentNode.php)
* [`Text`](src/Visitor/Text.php)

## Cache parsed trees

If for some reason your code call the reader multiple times for the same stream object, you may want to cache the parsed tree in order to save some time. You can do so as shown below:

```php
$services = bootstrap();
$cache = $services['cache']($services['reader']);
$xml = '<root><foo some="attribute"/></root>';
$tree = $cache(
    $stream = new StringStream($xml)
);
$tree2 = $cache($stream);
$tree3 = $cache(
    new StringStream($xml)
);
```

Here `$tree` and `$tree2` are the exact same node instance, however `$tree3` doesn't represent the same instance as, even though it's the same xml, it's not the same stream object instance.

When you know you no longer need a tree to be kept in the cache you can call `$services['cache_storage']->remove($stream)` in order to remove the internal reference to both the stream and the associated tree.
