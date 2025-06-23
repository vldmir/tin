[![Latest Stable Version](https://img.shields.io/packagist/v/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![GitHub stars](https://img.shields.io/github/stars/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![Total Downloads](https://img.shields.io/packagist/dt/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![GitHub Workflow Status][github workflow status]][github actions link]
[![Type Coverage](https://shepherd.dev/github/vldmir/tin/coverage.svg)](https://shepherd.dev/github/vldmir/tin)
[![License](https://img.shields.io/packagist/l/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)

# Enhanced Taxpayer Identification Number (TIN) Validator

## Description

An enhanced library to validate TIN numbers for individuals with advanced features including input masks, TIN type identification, and comprehensive Docker support. This is a fork of the original loophp/tin library with significant enhancements.

**New Features in v2.0.0:**
- 🎯 **Input Mask Support** - Get format masks for TIN input fields
- 📝 **Placeholder Generation** - Generate example values for input fields  
- 🔧 **Input Formatting** - Format user input according to TIN mask
- 🏷️ **TIN Type Identification** - Identify specific TIN types (DNI, NIE, CIF, etc.)
- 📊 **TIN Types API** - Get available TIN types for each country
- 🐳 **Docker Support** - Complete development environment with PHP 8.3
- 📚 **Enhanced Documentation** - Comprehensive examples and usage guides

Supported countries:

- Austria (AT)
- Belgium (BE)
- Bulgaria (BG)
- Croatia (HR)
- Cyprus (CY)
- Czech Republic (CZ)
- Denmark (DK)
- Estonia (EE)
- Finland (FI)
- France (FR)
- Germany (DE)
- Greece (GR) - only size
- Hungary (HU)
- Ireland (IE)
- Italy (IT)
- Latvia (LV) - no check digit
- Lithuania (LT)
- Luxembourg (LU)
- Malta (MT) - no check digit
- Netherlands (NL)
- Poland (PL)
- Portugal (PT)
- Romania (RO) - no check digit
- Slovakia (SK)
- Slovenia (SI)
- [Spain (ES)](docs/TIN-Country_Sheet_ES.md)
- Sweden (SE)
- United Kingdom (UK) - only structure

If your country is not there, feel free to open an issue with your country code,
and a link to the specification. Ideally, you can provide a pull request with
the algorithm and the tests.

## Requirements

- PHP >= 8.1

## Quick Start

### Installation

```bash
composer require vldmir/tin
```

### Basic Validation

To simply check the validity of a TIN number:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use loophp\Tin\TIN;

$bool = TIN::fromSlug('be71102512345')->isValid();
```

If you want to get the reason why a number is invalid, you can use:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use loophp\Tin\TIN;
use loophp\Tin\Exception\TINException;

try {
    TIN::fromSlug('be71102512345')->check();
} catch (TINException $e) {
    echo "Validation Error: " . $e->getMessage();
}
```

## Advanced Features

### Input Masks and Formatting

The library provides comprehensive input mask and formatting capabilities:

```php
<?php

use loophp\Tin\TIN;

// Get the input mask for a country
$tin = TIN::fromSlug('be71102512345');
$mask = $tin->getInputMask(); // Returns: "99.99.99-999.99"

// Get a placeholder example
$placeholder = $tin->getPlaceholder(); // Returns: "85.07.30-033.61"

// Format raw input according to the country's mask
$formatted = $tin->formatInput('71102512345'); // Returns: "71.10.25-123.45"

// Get mask information without creating a TIN instance
$maskInfo = TIN::getMaskForCountry('BE');
// Returns: [
//     'mask' => '99.99.99-999.99', 
//     'placeholder' => '85.07.30-033.61',
//     'country' => 'BE'
// ]
```

### TIN Type Identification

Different countries may have multiple types of TINs. The library can identify and categorize them:

```php
<?php

use loophp\Tin\TIN;

// Get all TIN types for a country
$types = TIN::getTinTypesForCountry('ES');
// Returns:
// [
//     1 => ['code' => 'DNI', 'name' => 'Documento Nacional de Identidad', 'description' => 'Spanish Natural Persons ID'],
//     2 => ['code' => 'NIE', 'name' => 'Número de Identidad de Extranjero', 'description' => 'Foreigners Identification Number'],
//     3 => ['code' => 'CIF', 'name' => 'Código de Identificación Fiscal', 'description' => 'Tax Identification Code for Legal Entities']
// ]

// Identify the type of a specific TIN
$tin = TIN::fromSlug('es12345678Z');
$type = $tin->identifyTinType(); 
// Returns: ['code' => 'DNI', 'name' => 'Documento Nacional de Identidad', 'description' => 'Spanish Natural Persons ID']

// Get TIN types for the current TIN's country
$allTypes = $tin->getTinTypes();
```

### Complete Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use loophp\Tin\TIN;
use loophp\Tin\Exception\TINException;

// Test different countries
$testCases = [
    ['country' => 'BE', 'tin' => '71102512345', 'description' => 'Belgian TIN'],
    ['country' => 'ES', 'tin' => '12345678Z', 'description' => 'Spanish DNI'],
    ['country' => 'DE', 'tin' => '12345678901', 'description' => 'German TIN'],
    ['country' => 'UK', 'tin' => 'AB123456C', 'description' => 'UK TIN'],
];

foreach ($testCases as $test) {
    echo "Testing {$test['description']} ({$test['country']}): {$test['tin']}\n";
    
    try {
        $tin = TIN::fromSlug($test['country'] . $test['tin']);
        
        // Basic validation
        $isValid = $tin->isValid();
        echo "Valid: " . ($isValid ? 'YES' : 'NO') . "\n";
        
        // Get input mask and placeholder
        $mask = $tin->getInputMask();
        $placeholder = $tin->getPlaceholder();
        echo "Input Mask: $mask\n";
        echo "Placeholder: $placeholder\n";
        
        // Format input
        $formatted = $tin->formatInput($test['tin']);
        echo "Formatted: $formatted\n";
        
        // Identify TIN type
        $tinType = $tin->identifyTinType();
        if ($tinType) {
            echo "TIN Type: {$tinType['code']} - {$tinType['name']}\n";
            echo "Description: {$tinType['description']}\n";
        }
        
        echo "Validation: PASSED\n";
        
    } catch (TINException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
```

### Examples by Country

| Country | Mask | Placeholder | TIN Types |
|---------|------|-------------|-----------|
| Belgium (BE) | `99.99.99-999.99` | `85.07.30-033.61` | TIN |
| Spain (ES) | `99999999A` | `12345678Z` | DNI, NIE, CIF |
| Germany (DE) | `999 999 999 99` | `123 456 789 01` | IdNr, StNr |
| United Kingdom (UK) | `AA999999A` | `AB123456C` | UTR, NINO |
| France (FR) | `9 99 99 99 999 999` | `1 23 45 67 890 123` | TIN |
| Italy (IT) | `AAAAAANNANNANAAA` | `RSSMRA85T10A562S` | TIN |

**Mask Format:**
- `9` - Digit (0-9)
- `A` - Uppercase letter
- `a` - Lowercase letter
- Other characters (`.`, `-`, space) - Separators

## Docker Development Environment

The library includes a complete Docker setup for easy development and testing:

### Quick Start with Docker

```bash
# Start containers and install dependencies
make up

# Run tests
make phpspec

# Run the test script
make tin-test

# Open shell in container
make shell
```

### Available Commands

```bash
make up          # Start containers and install dependencies
make down        # Stop containers
make shell       # Open bash shell in PHP container
make composer    # Run composer commands
make test        # Run all tests (PHPSpec + GrumPHP)
make phpspec     # Run PHPSpec tests
make grumphp     # Run GrumPHP checks
make phpstan     # Run PHPStan analysis
make psalm       # Run Psalm analysis
make infection   # Run mutation testing
make tin-test    # Run the TIN test script
```

### Manual Docker Commands

```bash
# Start containers
docker-compose up -d

# Install dependencies
docker-compose run --rm tin-composer composer install

# Run tests
docker exec tin-php vendor/bin/phpspec run -vvv --stop-on-failure

# Run the TIN test script
docker exec tin-php php test-tin.php

# Open shell
docker exec -it tin-php bash
```

For detailed Docker documentation, see [DOCKER.md](DOCKER.md).

## Strict Mode

If you want to use a stricter method (without normalizing the TIN number, that
is, using the raw TIN number), use the `strict` parameter in the `check` or
`isValid` functions as shown below. By default, it is set to `false`.

```php
TIN::fromSlug('be7110.2512345')->check(); // Not strict
TIN::fromSlug('be7110.2512345')->check(strict: false); // Not strict
TIN::fromSlug('be7110.2512345')->check(true); // Strict
TIN::fromSlug('be7110.2512345')->check(strict: true); // Strict
```

## Code Quality, Tests and Benchmarks

Every time changes are introduced into the library,
[Github](https://github.com/vldmir/tin/actions) run the tests and the
benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/). Feel free
to check them out in the `spec` directory. Run `composer phpspec` to trigger the
tests.

Before each commit some inspections are executed with
[GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to
check manually.

[PHPInfection](https://github.com/infection/infection) is used to ensure that
your code is properly tested, run `composer infection` to test your code.

### Running Tests

```bash
# Using Composer
composer phpspec

# Using Docker
make phpspec

# With code coverage
docker exec tin-php vendor/bin/phpspec run -vvv --stop-on-failure
```

## Links

- [`European Commission TIN service`](https://ec.europa.eu/taxation_customs/tin/)
- [`TIN Algorithms - Public - Functional Specification`](https://ec.europa.eu/taxation_customs/tin/specs/FS-TIN%20Algorithms-Public.docx)
- [`Taxpayer Identification Number`](https://en.wikipedia.org/wiki/Taxpayer_Identification_Number)

## Authors

- [Volodymyr Romantsov](https://github.com/vldmir) - Enhanced version with input masks and Docker support
- [Thomas Portelange](https://github.com/lekoala) - Original library
- [Pol Dellaiera](https://github.com/loophp) - Original library

## Contributing

We warmly welcome your contributions by submitting pull requests. Our team is
highly responsive and will gladly guide you through the entire process, from the
initial submission to the final resolution.

### Development Setup

1. Fork the repository
2. Clone your fork
3. Set up the Docker environment: `make up`
4. Make your changes
5. Run tests: `make phpspec`
6. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

[github workflow status]:
  https://img.shields.io/github/actions/workflow/status/vldmir/tin/tests.yml?branch=master&style=flat-square
[github sponsors link]: https://github.com/vldmir/tin/graphs/contributors
[github actions link]: https://github.com/vldmir/tin/actions
