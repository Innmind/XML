# Changelog

## [Unreleased]

### Added

- `Innmind\Xml\Element\Name`
- Support for the new PHP `8.4` `\Dom\*` API
- `Innmind\Xml\Format`

### Changed

- Requires `innmind/filesystem:~8.1`
- `Innmind\Xml\Element::name()` now returns a `Innmind\Xml\Element\Name`
- `Innmind\Xml\Element\Element::of()` name argument now expects a `Name`
- `Innmind\Xml\Element\SelfClosingElement::of()` name argument now expects a `Name`
- `Innmind\Xml\Element` is now a final class
- `Innmind\Xml\Element` no longer extends `Innmind\Xml\Node`
- `Innmind\Xml\Node\Document` has been renamed `Innmind\Xml\Document`
- `Innmind\Xml\Node\Document\Version` has been renamed `Innmind\Xml\Document\Version`
- `Innmind\Xml\Node\Document\Type` has been renamed `Innmind\Xml\Document\Type`
- `Innmind\Xml\Node\Document\Encoding` has been renamed `Innmind\Xml\Document\Encoding`
- `Innmind\Xml\Node` is now a final class
- `Innmind\Xml\Node\CharacterData` is now internal, use `Innmind\Xml\Node::characterData()` instead
- `Innmind\Xml\Node\Comment` is now internal, use `Innmind\Xml\Node::comment()` instead
- `Innmind\Xml\Node\EntityReference` is now internal, use `Innmind\Xml\Node::entityReference()` instead
- `Innmind\Xml\Node\ProcessingInformation` is now internal, use `Innmind\Xml\Node::processingInformation()` instead
- `Innmind\Xml\Node\Text` is now internal, use `Innmind\Xml\Node::text()` instead
- `Innmind\Xml\Reader` is now a final class
- `Innmind\Xml\Document\Encoding` is now an enum that only supports `utf-8` and `ascii`

### Removed

- `Innmind\Xml\Element\Element` use `Innmind\Xml\Element` instead
- `Innmind\Xml\Element\SelfClosingElement` use `Innmind\Xml\Element` instead
- `Innmind\Xml\Node::children()`
- `Innmind\Xml\Node::filterChild()`
- `Innmind\Xml\Node::mapChild()`
- `Innmind\Xml\Node::prependChild()`
- `Innmind\Xml\Node::appendChild()`
- `Innmind\Xml\AsContent`
- `Innmind\Xml\Attribute::empty()`
- `Innmind\Xml\Attribute::toString()`
- `Innmind\Xml\Visitor\Text`
- `Innmind\Xml\Document::toString()`, use `->asContent(Format::inline)->toString()` instead

### Fixed

- PHP `8.4` deprecations
- Documents/nodes/elements are now properly rendered as strings

## 7.7.0 - 2024-06-26

### Changed

- Requires `innmind/immutable:~5.7`
- `Document` and `Element` children `Sequence` is no longer forced to be lazy, it depends on the kind of `Sequence` you use when building them

## 7.6.0 - 2023-10-22

### Changed

- Requires `innmind/filesystem:~7.0`

## 7.5.0 - 2023-09-16

### Added

- Support for `innmind/immutable:~5.0`

### Removed

- Support for PHP `8.1`

## 7.4.0 - 2023-02-03

### Added

- `Innmind\Xml\Attribute::empty()`

## 7.3.0 - 2023-01-21

### Added

- `Innmind\Xml\Node\ProcessingInstruction`

## 7.2.0 - 2022-12-18

### Added

- Support for `innmind/filesystem:~6.0`

## 7.1.0 - 2022-11-26

### Added

- `Innmind\Xml\AsContent` interface implemented on `Innmind\Xml\Node\Document` and `Innmind\Xml\Element\Element`

### Fixed

- Calling `prependChild` on `Node\Document` and `Element\Element` won't unwrap all the children in memory in case of a lazy sequence
