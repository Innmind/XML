# Changelog

## [Unreleased]

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
