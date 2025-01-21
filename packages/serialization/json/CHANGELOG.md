# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

### Changed

## [1.3.1]

### Added

### Changed
- fix(serialization): Float serialization also accepts integer values. [#85](https://github.com/microsoft/kiota-serialization-json-php/pull/85)

## [1.3.0]

### Added

### Changed
- fix(logic): Don't cast types since this might introduce some logical bugs. Make sure values match possible values for that type.
- fix(serialization): Fix how composed types are handled.

## [1.0.1]

### Changed
- Exclude non-prod files from shipped archive

## [1.0.0] - 2023-11-01

### Changed
- Return only non-null values within deserialized collections
- Bumped abstractions to 1.0.0
- Mark package as stable

## [0.7.0] - 2023-10-30

### Added
- Adds CHANGELOG. [#47](https://github.com/microsoft/kiota-serialization-json-php/pull/47)

### Changed
- Disabled PHP-HTTP discovery plugin. [#49](https://github.com/microsoft/kiota-serialization-json-php/pull/49)
- Bumps Kiota Abstractions dependency version. [#52](https://github.com/microsoft/kiota-serialization-json-php/pull/52)

## [0.6.0] - 2023-06-29

### Added
- Disable pipeline runs for forks. [#38](https://github.com/microsoft/kiota-serialization-json-php/pull/38)

### Changed
- Handle null values when serializing intersection wrappers. [#39](https://github.com/microsoft/kiota-serialization-json-php/pull/39)

## [0.5.1] - 2023-06-12

### Changed
- Fix DateTime serialization. [#35](https://github.com/microsoft/kiota-serialization-json-php/pull/35)

## [0.5.0] - 2023-05-18

### Changed
- Bump abstractions. [#29](https://github.com/microsoft/kiota-serialization-json-php/pull/29)

## [0.4.3] - 2023-04-13

### Added
- Support deserializing objects to stream. [#22](https://github.com/microsoft/kiota-serialization-json-php/pull/22)

## [0.4.2] - 2023-03-22

### Changed
- Fix static analysis issues. [#17](https://github.com/microsoft/kiota-serialization-json-php/pull/17)
- Fix PHPStan failure when getting object value from JsonParseNode. [#18](https://github.com/microsoft/kiota-serialization-json-php/pull/18)

## [0.4.1] - 2023-03-07

### Changed
- fix: tab escaping. [#15](https://github.com/microsoft/kiota-serialization-json-php/pull/15)

## [0.4.0] - 2023-02-21

### Added
- Composed types (De)serialization support. [#6](https://github.com/microsoft/kiota-serialization-json-php/pull/6)
- Add SonarCloud Coverage reporting. [#10](https://github.com/microsoft/kiota-serialization-json-php/pull/10)
- Change workflow to use strategy matrix for PHP versions. [#8](https://github.com/microsoft/kiota-serialization-json-php/pull/8)

### Changed
- Bump Abstractions version. [#13](https://github.com/microsoft/kiota-serialization-json-php/pull/13)


*For previous releass, please see our [Release Notes](https://github.com/microsoft/kiota-serialization-json-php/releases)*
