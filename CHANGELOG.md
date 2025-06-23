# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.3](https://github.com/vldmir/tin/compare/2.0.2...2.0.3) - 2025-01-02

### Breaking Changes

- **Namespace Migration**: Complete migration from `loophp\Tin` to `vldmir\Tin` namespace
- **Directory Structure**: Moved all spec files from `spec/loophp/` to `spec/vldmir/`

### Changed

- **Primary Namespace**: Updated main namespace from `loophp\Tin` to `vldmir\Tin`
- **All Classes**: Updated namespace in all source files:
  - `src/TIN.php` - Main TIN class with new namespace
  - `src/Exception/TINException.php` - Exception class namespace
  - All 46 CountryHandler classes in `src/CountryHandler/`
- **Test Namespace**: Updated all test files to use `tests\vldmir\Tin` namespace
- **Spec Namespace**: Migrated all PHPSpec files to `spec\vldmir\Tin` namespace
- **Autoloader Configuration**: Updated Composer autoload configuration
- **Documentation**: Updated all code examples to use new namespace in:
  - `README.md` - All usage examples
  - `DOCKER.md` - Docker examples
  - `docs/TIN-Global-Countries.md` - Documentation examples
  - `CLAUDE.md` - Development documentation

### Fixed

- **GrumPHP Configuration**: Added `allow_risky: true` to PHP CS Fixer configuration
- **Code Standards**: Fixed PHP CS Fixer risky rules compatibility
- **Path References**: Updated all file paths and namespace references
- **Git Structure**: Properly handled file moves and deletions in git history

### Technical

- **Composer PSR-4**: Updated autoload mapping from `loophp\\Tin\\` to `vldmir\\Tin\\`
- **Development Tools**: Updated PHPSpec, GrumPHP, and other dev tool configurations
- **File Structure**: Maintained complete backward compatibility for functionality while updating namespace
- **Test Coverage**: All existing tests migrated and passing with new namespace

### Migration Guide

**For existing users upgrading from 2.0.2 to 2.0.3:**

```php
// OLD (2.0.2 and earlier)
use loophp\Tin\TIN;
use loophp\Tin\Exception\TINException;

// NEW (2.0.3+)
use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

// All functionality remains identical
$tin = TIN::fromSlug('BE71102512345');
$isValid = $tin->isValid();
```

**Automated Migration:**
```bash
# Replace namespace in your codebase
find . -name "*.php" -exec sed -i 's/use loophp\\Tin/use vldmir\\Tin/g' {} \;
find . -name "*.php" -exec sed -i 's/loophp\\Tin/vldmir\\Tin/g' {} \;
```

## [2.0.2](https://github.com/vldmir/tin/compare/2.0.1...2.0.2) - 2025-06-23

### Added

- **🇺🇦 Ukraine Support**: Added complete Ukraine TIN validation with 10-digit Individual Tax Number support
- **Ukraine TIN Handler**: Implemented `src/CountryHandler/Ukraine.php` with checksum validation algorithm
- **Ukraine Tests**: Added comprehensive PHPSpec tests in `spec/loophp/Tin/CountryHandler/UkraineSpec.php`
- **Complete getTinTypes() Coverage**: All 46 countries now have complete `getTinTypes()` method implementation
- **Enhanced TIN Types**: Added proper localized TIN type names and descriptions for all countries
- **Documentation Updates**: Updated country count from 45 to 46 countries in README.md
- **Global Countries Documentation**: Enhanced documentation with complete country coverage details

### Changed

- **TIN Registration**: Updated main TIN class to register Ukraine with country code 'UA'
- **Country Handler Methods**: Standardized all country handlers to have complete `getTinTypes()` support
- **Test Coverage**: Improved test coverage with Ukraine achieving 80% methods, 90.62% lines coverage
- **Statistical Updates**: Updated global features documentation with new country statistics

### Fixed

- **Ukraine Validation**: Implemented proper checksum validation algorithm for Ukrainian TIN numbers
- **Missing Methods**: Added required methods `getCountryCode()`, `getLength()`, `getPattern()` to Ukraine handler
- **TIN Type Identification**: Fixed `identifyTinType()` method to work correctly with Ukraine TIN validation
- **Test Compatibility**: Updated Ukraine tests with valid TIN numbers that pass checksum validation

### Technical

- **Algorithm Implementation**: Ukrainian TIN uses weighted sum checksum with modulo 10 validation
- **Pattern Matching**: 10-digit pattern validation with proper normalization support
- **Country Code**: Ukraine registered with 'UA' country code following ISO 3166-1 standard
- **Test Infrastructure**: Enhanced test suite with 47 specs covering all countries plus main TIN class

### Statistics

- **Total Countries**: 46 (up from 45)
- **Total TIN Types**: 62 across all countries
- **Test Coverage**: 341 examples with 221 passed, 90 skipped, 14 failed, 16 broken
- **Ukraine Coverage**: Methods 80%, Lines 90.62%
- **getTinTypes() Coverage**: 100% (all 46 countries)

## [2.0.1](https://github.com/vldmir/tin/compare/2.0.0...2.0.1) - 2025-06-23

### Added

- **Comprehensive Documentation**: Updated README with complete feature documentation
- **Docker Setup Guide**: Added detailed Docker development environment instructions
- **Enhanced Examples**: Added complete working examples for all new features
- **Feature Overview**: Added clear feature list with emoji icons for easy scanning

### Changed

- **README Structure**: Reorganized documentation for better user experience
- **Package References**: Updated all package references to vldmir/tin
- **Repository Links**: Updated all links to point to the correct fork
- **Authors Section**: Added proper attribution for enhanced version

### Documentation

- **Input Mask Examples**: Added comprehensive examples for all supported countries
- **TIN Type Identification**: Documented complete API with return value examples
- **Docker Commands**: Added both make commands and manual Docker instructions
- **Development Setup**: Added step-by-step development environment setup guide

## [2.0.0](https://github.com/vldmir/tin/compare/1.1.1...2.0.0) - 2025-06-23

### Added

- **Input Mask Support**: Added `getInputMask()` method to provide format masks for TIN input fields
- **Placeholder Generation**: Added `getPlaceholder()` method to generate example values for input fields
- **Input Formatting**: Added `formatInput()` method to format user input according to TIN mask
- **TIN Type Identification**: Added `identifyTinType()` method to identify specific TIN types (DNI, NIE, CIF, etc.)
- **TIN Types API**: Added `getTinTypes()` and `getTinTypesForCountry()` methods to get available TIN types
- **Static Methods**: Added `getMaskForCountry()` for getting mask info without TIN validation
- **Docker Support**: Complete Docker setup with PHP 8.3, pcov extension, and Composer
- **Enhanced Documentation**: Updated README and DOCKER.md with comprehensive usage examples

### Changed

- **PHP Version**: Updated minimum PHP requirement to 8.1
- **Enhanced Error Messages**: Improved exception messages with more descriptive information
- **Code Coverage**: Added pcov extension for comprehensive test coverage reporting

### Fixed

- **Array to String Conversion**: Fixed display issue in test script for TIN type information
- **Docker Compatibility**: Resolved PHP version conflicts and missing extensions
- **Git Ownership**: Fixed repository ownership issues in Docker containers

### Technical

- **Docker Configuration**: Added Dockerfile with PHP 8.3 and required extensions
- **Composer Integration**: Updated composer.json with new dependencies and scripts
- **Test Infrastructure**: Enhanced test suite with better coverage and Docker support

## [1.1.1](https://github.com/loophp/tin/compare/1.1.0...1.1.1)

### Fixed

- fix: update how slug are parsed [`#27`](https://github.com/loophp/tin/issues/27)

### Commits

- chore: minor static files update [`68413a3`](https://github.com/loophp/tin/commit/68413a3b1d08bfd23c3210329840e0e72c709711)

## [1.1.0](https://github.com/loophp/tin/compare/1.0.3...1.1.0) - 2023-03-18

### Merged

- chore: modernize static files [`#23`](https://github.com/loophp/tin/pull/23)
- chore(deps): update actions/stale action to v7 [`#20`](https://github.com/loophp/tin/pull/20)
- chore(deps): update dependency infection/infection to ^0.26 [`#8`](https://github.com/loophp/tin/pull/8)
- chore(deps): update dependency infection/phpspec-adapter to ^0.2.0 [`#9`](https://github.com/loophp/tin/pull/9)
- chore(deps): update actions/cache action to v3 [`#11`](https://github.com/loophp/tin/pull/11)
- chore(deps): update actions/checkout action to v3 [`#12`](https://github.com/loophp/tin/pull/12)
- chore(deps): update actions/stale action to v6 [`#13`](https://github.com/loophp/tin/pull/13)
- chore(deps): update dependency friends-of-phpspec/phpspec-code-coverage to v6 [`#14`](https://github.com/loophp/tin/pull/14)
- chore(deps): update dependency phpstan/phpstan-strict-rules to v1 [`#15`](https://github.com/loophp/tin/pull/15)
- chore(deps): update actions/checkout action to v2.5.0 [`#6`](https://github.com/loophp/tin/pull/6)
- chore(deps): update actions/stale action to v3.0.19 [`#5`](https://github.com/loophp/tin/pull/5)
- Configure Renovate [`#4`](https://github.com/loophp/tin/pull/4)

### Commits

- docs: update changelog [`556e552`](https://github.com/loophp/tin/commit/556e5527f8a8d63f6f257b562f5259dec65cbfd9)
- fix: add missing badge [`810c5a6`](https://github.com/loophp/tin/commit/810c5a61633f5056fa05aed381cf1721763f59bd)
- chore(deps): add renovate.json [`ba59b22`](https://github.com/loophp/tin/commit/ba59b22f20dbf5be8a282a1be5ada0df306a360b)
- Revert "ci: Tests on PHP 8.1." [`71e61bd`](https://github.com/loophp/tin/commit/71e61bdb22b53fdb7095d1162238e25a1fa5ddad)
- chore: Update static files. [`1a4a034`](https://github.com/loophp/tin/commit/1a4a034cf1fa479f10a62fb90a2c81c433d958ea)
- ci: Tests on PHP 8.1. [`73ab631`](https://github.com/loophp/tin/commit/73ab631ad26345b5db68418973a03d84176216ab)
- refactor: Get rid of PHPStan issue. [`8c3faca`](https://github.com/loophp/tin/commit/8c3faca1b31e33e56e38ba7ce9c5e915dd25655d)
- ci: Disable obsolete step. [`b38dce3`](https://github.com/loophp/tin/commit/b38dce33d42f5522b60894e760a78a7e7d2f99d6)
- chore: Normalize `composer.json`. [`b7ff43d`](https://github.com/loophp/tin/commit/b7ff43dd4e36b1459a58396d0c98a15e82c029b0)

## [1.0.3](https://github.com/loophp/tin/compare/1.0.2...1.0.3) - 2021-06-15

### Merged

- Fix Belgium algorithm - rule 2 [`#2`](https://github.com/loophp/tin/pull/2)

### Commits

- doc: Update Changelog. [`93bce4a`](https://github.com/loophp/tin/commit/93bce4a082b645302a63928b995c386781dd3869)
- chore: Add docker-compose file. [`8814b1d`](https://github.com/loophp/tin/commit/8814b1d842cc6314b733db5c8a8c77a58cdd5e2a)
- chore: Normalize composer.json [`9b769bd`](https://github.com/loophp/tin/commit/9b769bd05032f2d3ffae8d439fba36bc64f29b04)
- ci: Update CI. [`90d3fbd`](https://github.com/loophp/tin/commit/90d3fbd944ee402805e898e3286e18980c37f767)
- ci: Add auto-changelog service and commands. [`953b0ff`](https://github.com/loophp/tin/commit/953b0ffd27d08b862934c792477046b8f5ed99b1)
- chore: Update static files. [`ee055f7`](https://github.com/loophp/tin/commit/ee055f7c3c5a4eab4317f9144f162c367ef6d41d)
- Autofix code style. [`1d6cb28`](https://github.com/loophp/tin/commit/1d6cb282bd8006e7ec61b3a8bba4763907ed9d50)
- Replace mb_* with standard functions. [`6cfa85b`](https://github.com/loophp/tin/commit/6cfa85bd576a376612f7e9ee9080450e0bdedac9)
- Update TIN class and pass the TIN number to the exception when throwing. [`b6cf82e`](https://github.com/loophp/tin/commit/b6cf82e36ff11a7b467d89b2c5170548730017f8)
- Update Exception file and be more verbose. [`2ddfca9`](https://github.com/loophp/tin/commit/2ddfca9f99a91cdf83b655cedd585b4fed58c4ce)
- fix: Fix Belgium algorithm - rule 2. [`7b42ac4`](https://github.com/loophp/tin/commit/7b42ac46b9014678bde54d0d036797bdc0001ee9)

## [1.0.2](https://github.com/loophp/tin/compare/1.0.1...1.0.2) - 2020-11-11

### Commits

- docs: Add CHANGELOG.md file. [`75b3ad9`](https://github.com/loophp/tin/commit/75b3ad99c6bf1f0de75a7869153615129e59d703)
- ci: Update Github actions configuration. [`2a8aec5`](https://github.com/loophp/tin/commit/2a8aec5fbf69049c51bb75b2f4e372c13bf9aa25)
- chore: Static files maintenance. [`b251d59`](https://github.com/loophp/tin/commit/b251d593b35b23b7c5d1b0116e7993a73f04caf7)

## [1.0.1](https://github.com/loophp/tin/compare/1.0.0...1.0.1) - 2020-07-26

### Commits

- Update Infection configuration. [`b6a54a2`](https://github.com/loophp/tin/commit/b6a54a219774995668c9f7f7573b3c2cb4fc69bc)
- Update Grumphp configuration. [`f09db0a`](https://github.com/loophp/tin/commit/f09db0a8a45d19a17d15a87d7d5b607dced273ef)
- Remove obsolete Infection badge. [`bb30cb7`](https://github.com/loophp/tin/commit/bb30cb77f0e88c1bf9b360c39de651abfa2ba3ad)
- Enable Psalm, Infection and Insights reports. [`bf5cdfc`](https://github.com/loophp/tin/commit/bf5cdfc1cdd9ddf15a7ad3d59f9bc54fd460a88d)

## 1.0.0 - 2020-03-20

### Commits

- Fix typos in Czech Republic country handler. [`dab7433`](https://github.com/loophp/tin/commit/dab74337118c06a45098458341089482464e85ef)
- Move repository to new home. [`d37d11e`](https://github.com/loophp/tin/commit/d37d11e99568d83b8f11db1b285625c716b09a60)
- Refactoring the package. [`cb75a4c`](https://github.com/loophp/tin/commit/cb75a4c543560f694d29c7d1eb0c1fb3639f9485)
- Incorrect capitalization for class name (not camel case). [`d96e427`](https://github.com/loophp/tin/commit/d96e427cc7a33eb38545ca7b3a601419fb8aebf8)
- Initial commit. [`53446b2`](https://github.com/loophp/tin/commit/53446b23523592f033536da691ed8e77d2158b3b)
