# Global Tax Identification Number (TIN) Formats - Non-EU Countries

This document provides comprehensive information about Tax Identification Number formats for countries outside the European Union that have been added to the TIN validation library.

## Table of Contents

1. [Argentina (AR)](#argentina-ar)
2. [Australia (AU)](#australia-au)
3. [Brazil (BR)](#brazil-br)
4. [Canada (CA)](#canada-ca)
5. [China (CN)](#china-cn)
6. [India (IN)](#india-in)
7. [Indonesia (ID)](#indonesia-id)
8. [Japan (JP)](#japan-jp)
9. [Mexico (MX)](#mexico-mx)
10. [Nigeria (NG)](#nigeria-ng)
11. [Russia (RU)](#russia-ru)
12. [Saudi Arabia (SA)](#saudi-arabia-sa)
13. [South Africa (ZA)](#south-africa-za)
14. [South Korea (KR)](#south-korea-kr)
15. [Switzerland (CH)](#switzerland-ch)
16. [Turkey (TR)](#turkey-tr)
17. [Ukraine (UA)](#ukraine-ua)
18. [United States (US)](#united-states-us)

---

## Argentina (AR)

### CUIT (Clave Única de Identificación Tributaria)

**Format**: `99-99999999-9` (11 digits)
**Pattern**: 2 digits (type) + 8 digits (number) + 1 digit (checksum)

#### Type Prefixes:
- **20, 23, 24, 27**: Individual taxpayers
- **30, 33, 34**: Companies and organizations

#### Validation:
- Uses modulo 11 checksum algorithm
- First two digits must be valid type prefix
- Cannot be all zeros

#### Example:
```php
$tin = TIN::from('AR', '20-12345678-9');
if ($tin->isValid()) {
    echo "Valid CUIT";
}
```

---

## Australia (AU)

### TFN (Tax File Number)
**Format**: 8-9 digits
**Validation**: Weighted checksum for 8-digit TFNs

### ABN (Australian Business Number)
**Format**: `99 999 999 999` (11 digits)
**Validation**: Modulo 89 checksum algorithm

#### Example:
```php
// TFN
$tfn = TIN::from('AU', '865414088');

// ABN
$abn = TIN::from('AU', '53004085616');
```

---

## Brazil (BR)

### CPF (Cadastro de Pessoas Físicas)
**Format**: `999.999.999-99` (11 digits)
**Use**: Individual taxpayers
**Validation**: Modulo 11 checksum with two check digits

### CNPJ (Cadastro Nacional da Pessoa Jurídica)
**Format**: `99.999.999/9999-99` (14 digits)
**Use**: Business entities
**Validation**: Weighted modulo 11 checksum

#### Example:
```php
// CPF
$cpf = TIN::from('BR', '123.456.789-09');

// CNPJ
$cnpj = TIN::from('BR', '11.222.333/0001-81');
```

---

## Canada (CA)

### SIN (Social Insurance Number)
**Format**: `999-999-999` (9 digits)
**Validation**: Luhn algorithm
**Restrictions**: Cannot start with 0, 8, or 9

### BN (Business Number)
**Format**: `999999999` or `999999999RC0001` (with program account)
**Validation**: Basic format validation

#### Example:
```php
// SIN
$sin = TIN::from('CA', '130-692-544');

// BN with program account
$bn = TIN::from('CA', '123456789RC0001');
```

---

## China (CN)

### Personal ID Number
**Format**: 18 digits (or 17 + X)
**Structure**: 
- 6 digits: Region code
- 8 digits: Birth date (YYYYMMDD)
- 3 digits: Sequence code
- 1 character: Check digit (0-9 or X)

### Unified Social Credit Code (Business)
**Format**: 18 alphanumeric characters
**Valid Characters**: 0-9, A-H, J-N, P-R, T-U, W-Y (excludes I, O, S, V, Z)

#### Example:
```php
// Personal ID
$personalId = TIN::from('CN', '11010519491231002X');

// Business Code
$businessCode = TIN::from('CN', '91110108MA01A3F52F');
```

---

## India (IN)

### PAN (Permanent Account Number)
**Format**: `AAAAA9999A` (10 characters)
**Structure**:
- 3 letters: Random
- 1 letter: Holder type (P=Individual, C=Company, etc.)
- 1 letter: Name initial
- 4 digits: Sequential number
- 1 letter: Check character

#### Holder Types:
- **P**: Individual
- **C**: Company
- **F**: Firm/LLP
- **A**: AOP (Association of Persons)
- **T**: Trust
- **H**: HUF (Hindu Undivided Family)
- **G**: Government
- **J**: Artificial Juridical Person
- **L**: Local Authority
- **B**: BOI (Body of Individuals)

#### Example:
```php
$pan = TIN::from('IN', 'AFZPK7190K');
```

---

## Indonesia (ID)

### NPWP (Nomor Pokok Wajib Pajak)
**Format**: `99.999.999.9-999.999` (16 digits)
**Structure**:
- 2 digits: Tax office code
- 6 digits: Registration number
- 1 digit: Check digit
- 3 digits: Branch code (000 for head office)
- 3 digits: Status code

#### Example:
```php
$npwp = TIN::from('ID', '01.234.567.8-901.234');
```

---

## Japan (JP)

### My Number (Individual)
**Format**: 12 digits
**Validation**: Check digit algorithm

### Corporate Number
**Format**: 13 digits
**Validation**: Modulo 9 check digit
**Restriction**: First digit cannot be 0

#### Example:
```php
// My Number
$myNumber = TIN::from('JP', '123456789018');

// Corporate Number
$corporateNumber = TIN::from('JP', '1234567890123');
```

---

## Mexico (MX)

### RFC (Registro Federal de Contribuyentes)

#### Personal RFC
**Format**: `AAAA999999XXX` (13 characters)
**Structure**:
- 4 letters: Name components
- 6 digits: Birth date (YYMMDD)
- 3 alphanumeric: Homoclave + check digit

#### Business RFC
**Format**: `AAA999999XXX` (12 characters)
**Structure**:
- 3 letters: Company name abbreviation
- 6 digits: Registration date (YYMMDD)
- 3 alphanumeric: Homoclave + check digit

#### Example:
```php
// Personal
$rfcPersonal = TIN::from('MX', 'GODE561231GR8');

// Business
$rfcBusiness = TIN::from('MX', 'ABC010203AB1');
```

---

## Nigeria (NG)

### TIN (Tax Identification Number)
**Format**: 10 digits
**Validation**: 
- Cannot start with 0
- Cannot be all same digits

#### Example:
```php
$tin = TIN::from('NG', '1234567890');
```

---

## Russia (RU)

### INN (Taxpayer Identification Number)

#### Personal INN
**Format**: 12 digits
**Structure**:
- 2 digits: Region code (01-99)
- 2 digits: Tax office code
- 6 digits: Record number
- 2 digits: Check digits

#### Company INN
**Format**: 10 digits
**Structure**:
- 2 digits: Region code (01-99)
- 2 digits: Tax office code
- 5 digits: Record number
- 1 digit: Check digit

#### Example:
```php
// Personal
$personalInn = TIN::from('RU', '500100732259');

// Company
$companyInn = TIN::from('RU', '7707083893');
```

---

## Saudi Arabia (SA)

### Tax Identification Number (VAT Registration)
**Format**: 15 digits starting with 3
**Validation**: Modulo 11 checksum on last digit

#### Example:
```php
$tin = TIN::from('SA', '300123456789015');
```

---

## South Africa (ZA)

### Income Tax Reference Number
**Format**: 10 digits
**Restriction**: Must start with 0, 1, 2, 3, or 9
**Validation**: Luhn algorithm

#### Example:
```php
$tin = TIN::from('ZA', '0001339050');
```

---

## South Korea (KR)

### RRN (Resident Registration Number)
**Format**: `999999-9999999` (13 digits)
**Structure**:
- 6 digits: Birth date (YYMMDD)
- 1 digit: Gender/century code
- 6 digits: Registration data
- 1 digit: Check digit

#### Gender/Century Codes:
- **1, 2**: Korean born 1900-1999 (1=male, 2=female)
- **3, 4**: Korean born 2000-2099 (3=male, 4=female)
- **5, 6**: Foreigner born 1900-1999 (5=male, 6=female)
- **7, 8**: Foreigner born 2000-2099 (7=male, 8=female)
- **9, 0**: Born 1800-1899

### BRN (Business Registration Number)
**Format**: `999-99-99999` (10 digits)
**Validation**: Weighted checksum algorithm

#### Example:
```php
// RRN
$rrn = TIN::from('KR', '900101-1234563');

// BRN
$brn = TIN::from('KR', '220-86-05173');
```

---

## Switzerland (CH)

### AVS/AHV Number (Social Security)
**Format**: `756.9999.9999.99` (13 digits)
**Validation**: EAN-13 checksum
**Note**: Must start with 756 (Swiss country code)

### UID (Business Identification)
**Format**: `CHE-999.999.999`
**Validation**: Modulo 11 checksum

#### Example:
```php
// AVS/AHV
$avs = TIN::from('CH', '756.1234.5678.90');

// UID
$uid = TIN::from('CH', 'CHE-123.456.789');
```

---

## Turkey (TR)

### T.C. Kimlik No (National ID)
**Format**: 11 digits
**Validation**: 
- Cannot start with 0
- Two check digits using specific algorithm

### Vergi Kimlik No (Business Tax ID)
**Format**: 10 digits
**Validation**: Complex weighted checksum

#### Example:
```php
// Personal
$tckn = TIN::from('TR', '10000000146');

// Business
$vkn = TIN::from('TR', '1234567890');
```

---

## Ukraine (UA)

### Individual Tax Number
**Format**: 10 digits
**Validation**: 
- Weighted checksum algorithm (positions 1-9 weighted by 1-9)
- Modulo 11 calculation with final modulo 10
- Cannot be all zeros or all same digits

#### Validation Algorithm:
1. Multiply each of the first 9 digits by its position weight (1-9)
2. Sum all weighted values
3. Calculate remainder when divided by 11
4. Calculate final check digit as remainder modulo 10
5. Compare with the 10th digit

#### Example:
```php
$tin = TIN::from('UA', '1234567890');
if ($tin->isValid()) {
    echo "Valid Ukrainian TIN";
}
```

---

## United States (US)

### SSN (Social Security Number)
**Format**: `999-99-9999` (9 digits)
**Restrictions**:
- Cannot have all zeros in any group
- Cannot start with 666 or 900-999 (except ITIN)
- Invalid area codes: 123, 456, 789

### ITIN (Individual Taxpayer Identification Number)
**Format**: `999-99-9999` (9 digits)
**Requirements**:
- Must start with 9
- 4th-5th digits must be: 50-65, 70-88, 90-92, 94-99

### EIN (Employer Identification Number)
**Format**: `99-9999999` (9 digits)
**Validation**: First two digits must be valid campus code

#### Example:
```php
// SSN
$ssn = TIN::from('US', '123-45-6789');

// ITIN
$itin = TIN::from('US', '950-70-1234');

// EIN
$ein = TIN::from('US', '12-3456789');
```

---

## Usage Examples

### Basic Validation
```php
use vldmir\Tin\TIN;

// Create TIN from country code + number
$tin = TIN::from('BR', '12345678909');

// Create from country and TIN
$tin = TIN::from('BR', '12345678909');

// Validate
if ($tin->isValid()) {
    echo "Valid TIN";
}

// Check with strict validation (preserves formatting)
$tin->check(true);
```

### Get TIN Information
```php
// Get input mask for a country
$maskInfo = TIN::getMaskForCountry('BR');
// Returns: ['mask' => '999.999.999-99', 'placeholder' => '123.456.789-09', 'country' => 'BR']

// Get all TIN types for a country
$types = TIN::getTinTypesForCountry('US');
// Returns array of TIN types (SSN, ITIN, EIN)

// Identify specific TIN type
$tin = TIN::from('US', '950-70-1234');
$type = $tin->identifyTinType();
// Returns: ['code' => 'ITIN', 'name' => 'Individual Taxpayer Identification Number', ...]
```

### Format Input
```php
$tin = TIN::from('BR', '12345678909');
$formatted = $tin->formatInput('12345678909');
// Returns: '123.456.789-09'
```

### Country Support Check
```php
if (TIN::isCountrySupported('JP')) {
    echo "Japan is supported";
}
```

### Get All Supported Countries
```php
// Get simple list of country codes
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
echo "Total supported countries: $totalCountries"; // 45 countries
```

---

## Validation Rules Summary

| Country | Format(s) | Checksum | Special Rules |
|---------|-----------|----------|---------------|
| AR | 11 digits | Modulo 11 | Type prefix validation |
| AU | 8-9 or 11 digits | TFN: weighted, ABN: modulo 89 | ABN starts calculation with first digit -1 |
| BR | 11 or 14 digits | Modulo 11 (dual check) | CPF/CNPJ distinction by length |
| CA | 9 digits | Luhn (SIN only) | SIN cannot start with 0,8,9 |
| CN | 18 chars | Complex weighted | Personal has date validation |
| IN | 10 chars | None | 4th char indicates holder type |
| ID | 16 digits | None | Tax office code validation |
| JP | 12 or 13 digits | Custom algorithms | Length determines type |
| MX | 12 or 13 chars | Date validation | Contains birth/registration date |
| NG | 10 digits | None | Cannot start with 0 |
| RU | 10 or 12 digits | Weighted checksum | Region code validation |
| SA | 15 digits | Modulo 11 | Must start with 3 |
| ZA | 10 digits | Luhn | Must start with 0,1,2,3,9 |
| KR | 10 or 13 digits | Weighted checksum | RRN has date validation |
| CH | 13 chars or CHE+9 | EAN-13 or Modulo 11 | AVS starts with 756 |
| TR | 10 or 11 digits | Complex algorithms | Personal cannot start with 0 |
| UA | 10 digits | Weighted modulo 11 | Cannot be all zeros or same digits |
| US | 9 digits | Format validation | Multiple types with different rules | 