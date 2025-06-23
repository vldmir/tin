# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP library for validating Tax Identification Numbers (TINs) across European countries. It's an enhanced fork of loophp/tin that adds input masks and placeholders for TIN formats.

## Common Development Commands

### Testing
- **Run PHPSpec tests:** `composer phpspec` or `vendor/bin/phpspec run -vvv --stop-on-failure`
- **Run a single test:** `vendor/bin/phpspec run spec/loophp/Tin/CountryHandler/BelgiumSpec.php`
- **Generate code coverage:** Tests are configured to output coverage to `build/` directory

### Code Quality
- **Run all checks:** `composer grumphp` or `./vendor/bin/grumphp run`
- **Static analysis with PHPStan:** `vendor/bin/phpstan analyse`
- **Static analysis with Psalm:** `vendor/bin/psalm`
- **Mutation testing:** `composer infection` or `vendor/bin/infection run -j 2`

### Installation
- **Install dependencies:** `composer install`

## Architecture Overview

The library follows a handler-based architecture where each country has its own validation handler:

1. **Main Entry Point**: `src/TIN.php`
   - Static factory methods: `fromSlug()` and `from()`
   - Core validation methods: `isValid()` and `check()`
   - Enhanced methods: `getInputMask()`, `getPlaceholder()`, `formatInput()`

2. **Country Handlers**: `src/CountryHandler/`
   - Each country extends `CountryHandler` abstract class
   - Must define: `COUNTRYCODE`, `LENGTH`, `PATTERN`, and optionally `MASK`
   - Implements validation logic specific to each country's TIN format
   - Can override date validation, pattern matching, and checksum algorithms

3. **Key Interfaces**:
   - `CountryHandlerInterface`: Defines the contract for all country handlers
   - Methods: `supports()`, `validate()`, `getInputMask()`, `getPlaceholder()`

4. **Validation Flow**:
   - TIN is parsed to extract country code and number
   - Appropriate country handler is selected
   - Handler validates: length → pattern → date (if applicable) → checksum/rules
   - Throws `TINException` with specific error messages on validation failure

5. **Enhanced Features** (unique to this fork):
   - Input masks (e.g., Belgium: `99.99.99-999.99`)
   - Placeholder generation based on masks
   - Input formatting for display purposes

## Testing Approach

- Uses PHPSpec for behavior-driven development
- Each country handler has a corresponding spec file in `spec/loophp/Tin/CountryHandler/`
- Tests cover valid/invalid TINs, edge cases, and error conditions
- Mutation testing ensures test quality with minimum 50% MSI