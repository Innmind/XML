# Changelog

## [Unreleased]

### Added

- `Innmind\Xml\Element\Name`

### Changed

- Requires `innmind/filesystem:~8.1`
- `Innmind\Xml\Element::name()` now returns a `Innmind\Xml\Element\Name`
- `Innmind\Xml\Element\Element::of()` name argument now expects a `Name`
- `Innmind\Xml\Element\SelfClosingElement::of()` name argument now expects a `Name`
- `Innmind\Xml\Element` is now a final class
- `Innmind\Xml\Element` no longer extends `Innmind\Xml\Node`

### Removed

- `Innmind\Xml\Element\Element` use `Innmind\Xml\Element` instead
- `Innmind\Xml\Element\SelfClosingElement` use `Innmind\Xml\Element` instead

### Fixed

- PHP `8.4` deprecations

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
