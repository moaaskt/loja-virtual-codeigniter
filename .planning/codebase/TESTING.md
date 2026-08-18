# Testing

## Framework
The project uses **PHPUnit** (version ^10.5.16) for automated testing, which is the standard testing suite for CodeIgniter 4.

## Configuration
Testing configuration is defined in `phpunit.xml.dist` at the project root.

## Directory Structure
Tests are located in the `tests/` directory.

## Supporting Tools
- **FakerPHP**: Included in `require-dev` to generate fake data for testing and database seeding.
- **vfsStream**: Included for mocking the file system during tests (e.g., testing image uploads).

## Commands
Tests can be run using composer:
```bash
composer test
```
Or directly with PHPUnit if installed globally/locally.
