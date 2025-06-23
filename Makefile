# TIN Library Docker Commands (Similar to Laravel Sail)

.PHONY: help up down shell composer test phpspec grumphp phpstan psalm infection tin-test

help:
	@echo "Available commands:"
	@echo "  make up          - Start Docker containers"
	@echo "  make down        - Stop Docker containers"
	@echo "  make shell       - Open a shell in the PHP container"
	@echo "  make composer    - Run composer commands (e.g., make composer install)"
	@echo "  make test        - Run all tests"
	@echo "  make phpspec     - Run PHPSpec tests"
	@echo "  make grumphp     - Run GrumPHP checks"
	@echo "  make phpstan     - Run PHPStan analysis"
	@echo "  make psalm       - Run Psalm analysis"
	@echo "  make infection   - Run mutation testing"
	@echo "  make tin-test    - Run the TIN test script"

up:
	docker-compose up -d
	@echo "Containers started. Installing dependencies..."
	docker-compose run --rm tin-composer  install
	@echo "Ready! Use 'make shell' to enter the container."

down:
	docker-compose down

shell:
	docker exec -it tin-php bash

composer:
	docker-compose run --rm tin-composer $(filter-out $@,$(MAKECMDGOALS))

test: phpspec grumphp

phpspec:
	docker exec tin-php vendor/bin/phpspec run -vvv --stop-on-failure

grumphp:
	docker exec tin-php vendor/bin/grumphp run

phpstan:
	docker exec tin-php vendor/bin/phpstan analyse

psalm:
	docker exec tin-php vendor/bin/psalm

infection:
	docker exec tin-php vendor/bin/infection run -j 2

tin-test:
	docker exec tin-php php test-tin.php

# Prevent make from treating the arguments as targets
%:
	@:
