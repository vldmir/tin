[![Latest Stable Version](https://img.shields.io/packagist/v/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![GitHub stars](https://img.shields.io/github/stars/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![Total Downloads](https://img.shields.io/packagist/dt/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)
[![GitHub Workflow Status][github workflow status]][github actions link]
[![Type Coverage](https://shepherd.dev/github/vldmir/tin/coverage.svg)](https://shepherd.dev/github/vldmir/tin)
[![License](https://img.shields.io/packagist/l/vldmir/tin.svg?style=flat-square)](https://packagist.org/packages/vldmir/tin)

# Enhanced Tax Identification Numbers (TIN) Validator

## Description

An enhanced library to validate Tax Identification Numbers (TIN) for individuals and businesses worldwide with advanced features including input masks, TIN type identification, comprehensive Docker support, and extensive global coverage. This is a fork of the original loophp/tin library with significant enhancements and global expansion.

**Major Features:**
- 🌍 **Global Coverage** - Support for 46 countries including EU and major non-EU countries
- 🎯 **Input Mask Support** - Get format masks for TIN input fields
- 📝 **Placeholder Generation** - Generate example values for input fields  
- 🔧 **Input Formatting** - Format user input according to TIN mask
- 🏷️ **TIN Type Identification** - Identify specific TIN types (DNI, NIE, CIF, SSN, CPF, etc.)
- 📊 **TIN Types API** - Get available TIN types for each country (all 46 countries supported)
- 🔍 **Country Discovery** - Get all supported countries and their details
- ✅ **Advanced Validation** - Comprehensive checksum algorithms and format validation
- 🐳 **Docker Support** - Complete development environment with PHP 8.3
- 📚 **Enhanced Documentation** - Comprehensive examples and usage guides

**New in Latest Version:**
- 🌐 **18 New Non-EU Countries** - Added support for major global economies
- 🇺🇦 **Ukraine Support** - Individual Tax Number validation
- 🔢 **Enhanced Checksums** - Advanced validation algorithms for complex TIN formats
- 📋 **Country Information API** - Get detailed information about all supported countries
- 🎨 **Improved UI Support** - Better input masks and placeholders for web forms
- 🏷️ **Complete TIN Types Coverage** - All 46 countries now support getTinTypes() method with 62 total TIN types

Supported countries:

**European Union (EU) Countries:**
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

**Non-EU Countries:**
- Argentina (AR) - CUIT
- Australia (AU) - TFN, ABN
- Brazil (BR) - CPF, CNPJ
- Canada (CA) - SIN, BN
- China (CN) - Personal ID, Unified Social Credit Code
- India (IN) - PAN
- Indonesia (ID) - NPWP
- Japan (JP) - My Number, Corporate Number
- Mexico (MX) - RFC
- Nigeria (NG) - TIN
- Russia (RU) - INN
- Saudi Arabia (SA) - VAT registration number
- South Africa (ZA) - Income Tax Reference Number
- South Korea (KR) - RRN, BRN
- Switzerland (CH) - AVS/AHV, UID
- Turkey (TR) - T.C. Kimlik No, Vergi Kimlik No
- Ukraine (UA) - Individual Tax Number
- United Kingdom (UK) - only structure
- United States (US) - SSN, ITIN, EIN

**Total: 46 countries supported**

For detailed information about non-EU countries, see [TIN-Global-Countries.md](docs/TIN-Global-Countries.md).

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

### Complete TIN Types Support

**All 46 countries now support the `getTinTypes()` method!** Each country provides detailed information about their supported TIN types with proper local names and descriptions.

**📊 TIN Types Statistics:**
- **Total Countries**: 46
- **Total TIN Types**: 62 
- **Average Types per Country**: 1.35
- **Countries with Multiple Types**: 14

```php
<?php

use loophp\Tin\TIN;

// Every country supports getTinTypes() - here are some examples:

// EU Countries with specific local names
$belgiumTypes = TIN::getTinTypesForCountry('BE');
// Returns: [1 => ['code' => 'TIN', 'name' => 'Belgian TIN', 'description' => 'Belgian Tax Identification Number (Numéro de Registre National)']]

$italyTypes = TIN::getTinTypesForCountry('IT');
// Returns: [1 => ['code' => 'CF', 'name' => 'Italian CF', 'description' => 'Italian Fiscal Code (Codice Fiscale)']]

$netherlandsTypes = TIN::getTinTypesForCountry('NL');
// Returns: [1 => ['code' => 'BSN', 'name' => 'Dutch BSN', 'description' => 'Dutch Burgerservicenummer (BSN) - Citizen Service Number']]

// Global countries with proper local names
$ukraineTypes = TIN::getTinTypesForCountry('UA');
// Returns: [1 => ['code' => 'INDIVIDUAL_TAX_NUMBER', 'name' => 'Individual Tax Number', 'description' => 'Individual taxpayer identification number']]

$nigeriaTypes = TIN::getTinTypesForCountry('NG');
// Returns: [1 => ['code' => 'TIN', 'name' => 'Nigerian TIN', 'description' => 'Nigerian Tax Identification Number']]

$indonesiaTypes = TIN::getTinTypesForCountry('ID');
// Returns: [1 => ['code' => 'NPWP', 'name' => 'Indonesian NPWP', 'description' => 'Indonesian Tax Registration Number (Nomor Pokok Wajib Pajak)']]

// Countries with multiple TIN types
$usTypes = TIN::getTinTypesForCountry('US'); // 3 types: SSN, ITIN, EIN
$esTypes = TIN::getTinTypesForCountry('ES'); // 3 types: DNI, NIE, CIF
$brTypes = TIN::getTinTypesForCountry('BR'); // 2 types: CPF, CNPJ
```

### Get All Supported Countries

The library provides methods to get information about all supported countries:

```php
<?php

use loophp\Tin\TIN;

// Get simple list of all supported country codes
$countries = TIN::getSupportedCountries();
// Returns: ['AR', 'AT', 'AU', 'BE', 'BG', 'BR', 'CA', 'CH', 'CN', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'ID', 'IE', 'IN', 'IT', 'JP', 'KR', 'LT', 'LU', 'LV', 'MT', 'MX', 'NG', 'NL', 'PL', 'PT', 'RO', 'RU', 'SA', 'SE', 'SI', 'SK', 'TR', 'UA', 'UK', 'US', 'ZA']

// Get detailed information about all countries
$countriesWithDetails = TIN::getSupportedCountriesWithDetails();
// Returns array with detailed information for each country:
// [
//     'BR' => [
//         'country_code' => 'BR',
//         'mask' => '999.999.999-99',
//         'placeholder' => '123.456.789-09',
//         'length' => 11,
//         'pattern' => '^\d{11}$',
//         'tin_types' => [
//             1 => ['code' => 'CPF', 'name' => 'CPF', 'description' => 'Individual taxpayer identification number'],
//             2 => ['code' => 'CNPJ', 'name' => 'CNPJ', 'description' => 'Company taxpayer identification number']
//         ]
//     ],
//     // ... other countries
// ]

// Count total supported countries
$totalCountries = count(TIN::getSupportedCountries());
echo "Total supported countries: $totalCountries"; // 46 countries

// Check if a specific country is supported
if (TIN::isCountrySupported('UA')) {
    echo "Ukraine is supported!";
}
```

### Complete Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use loophp\Tin\TIN;
use loophp\Tin\Exception\TINException;

// Test different countries (EU and Global)
$testCases = [
    // EU Countries
    ['country' => 'BE', 'tin' => '71102512345', 'description' => 'Belgian TIN'],
    ['country' => 'ES', 'tin' => '12345678Z', 'description' => 'Spanish DNI'],
    ['country' => 'DE', 'tin' => '12345678901', 'description' => 'German TIN'],
    ['country' => 'UK', 'tin' => 'AB123456C', 'description' => 'UK TIN'],
    ['country' => 'FR', 'tin' => '1234567890123', 'description' => 'French TIN'],
    
    // Global Countries
    ['country' => 'US', 'tin' => '123-45-6789', 'description' => 'US Social Security Number'],
    ['country' => 'BR', 'tin' => '12345678909', 'description' => 'Brazilian CPF'],
    ['country' => 'CA', 'tin' => '130-692-544', 'description' => 'Canadian SIN'],
    ['country' => 'AU', 'tin' => '53004085616', 'description' => 'Australian ABN'],
    ['country' => 'MX', 'tin' => 'AABC560427MDF', 'description' => 'Mexican RFC'],
    ['country' => 'UA', 'tin' => '1234567890', 'description' => 'Ukrainian TIN'],
    ['country' => 'CN', 'tin' => '11010519491231002X', 'description' => 'Chinese Personal ID'],
    ['country' => 'IN', 'tin' => 'AFZPK7190K', 'description' => 'Indian PAN'],
    ['country' => 'JP', 'tin' => '123456789012', 'description' => 'Japanese My Number'],
    ['country' => 'RU', 'tin' => '123456789012', 'description' => 'Russian INN'],
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

// Demonstrate global country discovery
echo "=== Global Country Discovery ===\n";
$allCountries = TIN::getSupportedCountries();
echo "Total supported countries: " . count($allCountries) . "\n";

// Show countries with multiple TIN types
$countriesWithDetails = TIN::getSupportedCountriesWithDetails();
echo "\nCountries with multiple TIN types:\n";
foreach ($countriesWithDetails as $countryCode => $details) {
    if (count($details['tin_types']) > 1) {
        $types = array_map(function($type) { return $type['name']; }, $details['tin_types']);
        echo "$countryCode: " . implode(', ', $types) . "\n";
    }
}
```

### Examples by Country

| Country | Mask | Placeholder | TIN Types |
|---------|------|-------------|-----------|
| **EU Countries** |
| Belgium (BE) | `99.99.99-999.99` | `85.07.30-033.61` | TIN |
| Spain (ES) | `99999999A` | `12345678Z` | DNI, NIE, CIF |
| Germany (DE) | `999 999 999 99` | `123 456 789 01` | IdNr, StNr |
| United Kingdom (UK) | `AA999999A` | `AB123456C` | UTR, NINO |
| France (FR) | `9 99 99 99 999 999` | `1 23 45 67 890 123` | TIN |
| Italy (IT) | `AAAAAANNANNANAAA` | `RSSMRA85T10A562S` | TIN |
| **Global Countries** |
| United States (US) | `999-99-9999` | `123-45-6789` | SSN, ITIN, EIN |
| Brazil (BR) | `999.999.999-99` | `123.456.789-09` | CPF, CNPJ |
| Canada (CA) | `999-999-999` | `130-692-544` | SIN, BN |
| Australia (AU) | `99 999 999 999` | `53 004 085 616` | TFN, ABN |
| Mexico (MX) | `AAAA999999AAA` | `AABC560427MDF` | RFC Personal, RFC Business |
| Ukraine (UA) | `9999999999` | `1234567890` | Individual Tax Number |
| China (CN) | `999999999999999999` | `11010519491231002X` | Personal ID, Unified Social Credit Code |
| India (IN) | `AAAAA9999A` | `AFZPK7190K` | PAN |
| Japan (JP) | `999999999999` | `123456789012` | My Number, Corporate Number |
| Russia (RU) | `999999999999` | `123456789012` | INN Personal, INN Company |

**Mask Format:**
- `9` - Digit (0-9)
- `A` - Uppercase letter
- `a` - Lowercase letter
- Other characters (`.`, `-`, space) - Separators

## Global Features and Capabilities

### Multi-Country Support

The library now supports **46 countries** worldwide, including major global economies:

**🌍 Coverage by Region:**
- **Europe**: 27 EU countries + UK, Switzerland, Turkey, Ukraine
- **Americas**: US, Canada, Brazil, Argentina, Mexico
- **Asia-Pacific**: China, Japan, India, South Korea, Indonesia, Australia
- **Africa**: South Africa, Nigeria
- **Middle East**: Saudi Arabia

### Advanced Validation Algorithms

Each country implements sophisticated validation rules:

```php
// Complex checksum validation (Brazil CPF)
$cpf = TIN::fromSlug('BR12345678909');
$isValid = $cpf->isValid(); // Uses dual modulo 11 checksum

// Date-based validation (China Personal ID)
$chinaId = TIN::fromSlug('CN11010519491231002X');
$isValid = $chinaId->isValid(); // Validates birth date and checksum

// Multi-format support (US SSN/ITIN/EIN)
$usTin = TIN::fromSlug('US123-45-6789');
$type = $usTin->identifyTinType(); // Identifies SSN, ITIN, or EIN
```

### Business and Individual TIN Support

Many countries support both business and individual TIN types:

```php
// Brazil: CPF (individual) vs CNPJ (business)
$cpf = TIN::fromSlug('BR12345678909'); // Individual
$cnpj = TIN::fromSlug('BR11222333000181'); // Business

// Russia: INN Personal vs INN Company
$personalInn = TIN::fromSlug('RU123456789012'); // 12 digits
$companyInn = TIN::fromSlug('RU1234567890'); // 10 digits

// Japan: My Number vs Corporate Number
$myNumber = TIN::fromSlug('JP123456789012'); // Individual
$corporateNumber = TIN::fromSlug('JP1234567890123'); // Business
```

### Comprehensive Documentation

- **EU Countries**: Standard TIN validation
- **Global Countries**: See [TIN-Global-Countries.md](docs/TIN-Global-Countries.md) for detailed specifications
- **Validation Rules**: Each country includes specific algorithms and restrictions
- **Examples**: Real-world TIN examples for testing

### Complete TIN Types API

Every single country (all 46) now provides comprehensive TIN type information:

```php
// All countries support getTinTypes() with localized names
$allCountries = TIN::getSupportedCountries(); // 46 countries
$totalTypes = 0;
$countriesWithMultipleTypes = 0;

foreach ($allCountries as $country) {
    $types = TIN::getTinTypesForCountry($country);
    $totalTypes += count($types);
    
    if (count($types) > 1) {
        $countriesWithMultipleTypes++;
        echo "$country: " . count($types) . " types\n";
    }
}

echo "Total TIN types across all countries: $totalTypes\n"; // 62 types
echo "Countries with multiple TIN types: $countriesWithMultipleTypes\n"; // 14 countries
```

**Countries with Multiple TIN Types (14):**
- **US** (3): SSN, ITIN, EIN
- **ES** (3): DNI, NIE, CIF  
- **AU, BR, CA, CH, CN, DE, JP, KR, MX, RU, TR, UK** (2 each)

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
