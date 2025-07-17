# Docker Setup for TIN Library Testing

This setup provides a Laravel Sail-like experience for testing the TIN library using Docker.

## Prerequisites

- Docker and Docker Compose installed on your system
- Make command available (optional, but recommended)

## Quick Start

1. **Start the Docker containers:**
   ```bash
   make up
   # or without make:
   docker-compose up -d && docker-compose run --rm tin-composer composer install
   ```

2. **Run the test script:**
   ```bash
   make tin-test
   # or without make:
   docker exec tin-php php test-tin.php
   ```

3. **Run PHPSpec tests:**
   ```bash
   make phpspec
   # or without make:
   docker exec tin-php vendor/bin/phpspec run -vvv --stop-on-failure
   ```

## Available Commands

### Using Make (Recommended)

```bash
make up          # Start containers and install dependencies
make down        # Stop containers
make shell       # Open bash shell in PHP container
make composer    # Run composer commands (e.g., make composer update)
make test        # Run all tests (PHPSpec + GrumPHP)
make phpspec     # Run PHPSpec tests
make grumphp     # Run GrumPHP checks
make phpstan     # Run PHPStan analysis
make psalm       # Run Psalm analysis
make infection   # Run mutation testing
make tin-test    # Run the TIN test script
```

### Without Make

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

# Stop containers
docker-compose down
```

## Test Script

The `test-tin.php` script demonstrates:
- Basic TIN validation
- Input mask retrieval
- Placeholder generation
- Input formatting
- TIN type identification
- Static method usage

## Docker Services

- **tin-php**: PHP 8.3 CLI container with pcov extension for code coverage
- **tin-composer**: PHP 8.3 container with Composer for dependency management

## Technical Details

### PHP Version
- Uses PHP 8.3 (compatible with phpspec ^7)
- Includes pcov extension for code coverage
- Includes zip extension for Composer

### Extensions Installed
- `ext-pcov`: For code coverage generation
- `ext-zip`: For Composer package extraction

### Git Configuration
- Automatically configures git safe directory to avoid ownership issues

## Examples in Container

Once inside the container (`make shell`), you can run PHP directly:

```php
php -r "require 'vendor/autoload.php'; use vldmir\Tin\TIN; echo TIN::fromSlug('be71102512345')->getInputMask();"
```

Or create your own test files and run them:

```bash
echo '<?php
require "vendor/autoload.php";
use vldmir\Tin\TIN;

$tin = TIN::fromSlug("es12345678Z");
echo "Valid: " . ($tin->isValid() ? "Yes" : "No") . "\n";
echo "Mask: " . $tin->getInputMask() . "\n";
echo "Type: " . $tin->identifyTinType() . "\n";
' > mytest.php

php mytest.php
```

## Troubleshooting

### Permission Issues
If you encounter permission issues with vendor files:
```bash
sudo chown -R $(whoami):$(whoami) vendor composer.lock
```

### Rebuilding Containers
If you need to rebuild the containers:
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```