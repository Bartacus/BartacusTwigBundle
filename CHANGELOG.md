# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [3.0.0] - 2023-07-11
### Added
- TYPO3 v12.4 support

## [2.2.1] - 2023-02-27
### Fixed
- Drop deprecated / unused TSFE property

## [2.2.0] - 2023-02-27
### Changed
- Increase minimum composer package versions
- Fix php inspection warnings and errors

### Removed
- Drop TYPO3 v9 + TYPO3 v10 support
- Drop old documentation
- Drop php-cs-fixer
- Drop roave/security-advisories

## [2.1.0] - 2021-10-06
### Added
- TYPO3 v11.5 support

## [2.0.1] - 2021-03-29
### Changed
- Use `extra` instead of `replace` as mentioned in https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ComposerJson/Index.html#extra

## [2.0.0] - 2020-05-13
### Added
- Compatibility with  TYPO3 10.4 LTS 
- Compatibility with Twig 3.x
### Removed
- Compatibility with Twig 1.x
- Compatibility with TYPO3 8.7 LTS

## [1.0.4] - 2020-05-12
### Fixed
- Support for USER_INT

## [1.0.3] - 2019-05-16
### Fixed
- Add temporary conflicts with Twig 1.41.0|2.10.0 and up

## [1.0.2] - 2019-05-15
### Fixed
- Render ImmediateResponseException thrown by controller

## [1.0.1] - 2019-04-05
### Added
- Add documentation

## [1.0.0] - 2019-02-11
### Added
- `TWIGTEMPLATE` content object to render Twig templates in TypoScript
- `bartacus_cobject` Twig function to render TypoScript content objects from templates
- Compatible with TYPO3 8.7 LTS and 9.5 LTS
- Compatible with Symfony 3 and 4

[Unreleased]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/3.0.0...HEAD
[3.0.0]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/2.2.1...3.0.0
[2.2.1]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/2.0.1...2.1.0
[2.0.1]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/1.0.4...2.0.0
[1.0.4]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Bartacus/BartacusPlatformshBundle/compare/232cdda0...1.0.0
