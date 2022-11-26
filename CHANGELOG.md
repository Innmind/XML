# Changelog

## 7.1.0 - 2022-11-26

### Added

- `Innmind\Xml\AsContent` interface implemented on `Innmind\Xml\Node\Document` and `Innmind\Xml\Element\Element`

### Fixed

- Calling `prependChild` on `Node\Document` and `Element\Element` won't unwrap all the children in memory in case of a lazy sequence
