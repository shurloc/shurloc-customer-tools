# Shur-loc Customer Tools

Utilities for managing and enhancing Shur-loc® WooCommerce customer administration.

## Features

- Provide customer-focused administration tools for WooCommerce.
- Add customer information and utilities to the WordPress admin.
- Display customer cart information for administrative review.
- Provide reusable customer data formatting and presentation functionality.
- Integrate customer tools into the shared Shur-loc Tools administration interface.

## Requirements

- WordPress 7.0 or later
- WooCommerce
- Shur-loc Tools
- PHP 8.4 or later

## Installation

1. Install and activate **Shur-loc Tools**.
2. Install and activate **Shur-loc Customer Tools**.
3. Navigate to **Shur-loc Tools → Customers** in the WordPress admin.
4. Use the available customer tools as needed.

## Development

### Dependencies

Shur-loc Customer Tools depends on the shared **Shur-loc Tools** plugin for common infrastructure and admin interfaces.

For development, both repositories should be checked out as sibling directories:

```text
wordpress-plugins/
├── shurloc-tools/
└── shurloc-customer-tools/
```

This layout allows development and static-analysis tooling to resolve classes and interfaces provided by `shurloc-tools`.

Install the development dependencies with Composer:

```bash
composer install
```

### PHPUnit

The project includes PHPUnit unit tests covering customer administration functionality, customer data handling, formatting, and other plugin behavior.

Run the test suite:

```bash
composer test
```

### PHP_CodeSniffer

PHP_CodeSniffer is used to enforce the project's PHP coding standards.

Run code style checks:

```bash
composer lint
```

### PHPStan

PHPStan is used for static analysis of the plugin source and test suite.

Run static analysis:

```bash
composer phpstan
```

### Release Packages

A PowerShell build script is provided for creating distributable plugin packages:

```powershell
.\bin\build.ps1
```

Development files, tests, static-analysis configuration, and other files not required at runtime are excluded from release packages.

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.
