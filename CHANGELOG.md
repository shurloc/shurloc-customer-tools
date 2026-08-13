# Changelog

## [0.4.0] - 2026-08-13

### Added

- Added a **Phone** column to the WordPress Users screen.
- Added customer phone numbers from WooCommerce billing metadata.
- Added clickable `tel:` links for customer phone numbers.
- Added consistent display formatting for United States phone numbers.
- Added support for common United States phone number formats, including:
  - 10-digit numbers
  - 11-digit numbers with a leading `1`
  - Numbers with a `+1` country code
  - Hyphenated, dotted, parenthesized, and unformatted numbers
- Added normalized phone numbers for `tel:` links while preserving international country codes.
- Added defensive handling of missing and unrecognized phone numbers.
- Added PHPUnit coverage for:
  - Phone column registration
  - Phone column positioning
  - Missing phone numbers
  - United States phone number normalization
  - International phone numbers
  - `tel:` URI normalization
  - Phone number escaping
  - Unrecognized phone values

### Changed

- Positioned the **Phone** column immediately after the **Email** column on the WordPress Users screen.
- Standardized recognizable United States phone numbers as `(555) 123-4567` for display.
- Preserved non-U.S. international phone number formatting when displaying customer data.

### Internal Improvements

- Added a dedicated Users table column class for customer phone numbers.
- Kept phone number display normalization separate from stored WooCommerce `billing_phone` metadata.
- Separated human-readable phone formatting from `tel:` URI normalization.

### Testing

- Added PHPUnit coverage for the complete customer phone column feature.
- Added data-driven coverage for multiple United States phone number formats.
- Added coverage for international numbers and malformed phone values.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.3.0] - 2026-08-13

### Added

- Added customer purchase tracking for WooCommerce orders.
- Added **Last Purchase**, **Last Purchase Order**, **Last Purchase Status**, and **Last Purchase Total** values to user metadata.
- Added automatic purchase tracking for qualifying WooCommerce order status changes.
- Added protection against older orders overwriting a customer's more recent purchase metadata.
- Added **Last Purchase** column to the WordPress Users screen.
- Added relative-time formatting, linked order numbers, order status, and order totals to the **Last Purchase** column.
- Added sortable **Last Purchase** column.
- Added purchase filters for:
  - Purchased within 1 day
  - Purchased within 7 days
  - Purchased within 30 days
  - Not purchased within 1 day
  - Not purchased within 7 days
  - Not purchased within 30 days
  - Never purchased
- Added defensive handling of missing, empty, and zero-valued purchase timestamps.
- Added a shared Users-screen filter coordinator for customer filtering controls.
- Added PHPUnit coverage for:
  - User purchase tracking
  - Purchase metadata updates
  - Older-order protection
  - Order status changes
  - Users table purchase column
  - Purchase column sorting
  - Last Purchase filters
  - Existing user meta query preservation
  - Invalid filter values
  - Legacy and missing purchase metadata
  - Shared Users-screen filter coordination

### Changed

- Renamed `Shurloc_Activity_Time_Formatter` to `Shurloc_Relative_Time_Formatter` for reuse across customer timestamp displays.
- Refactored activity filters to use the shared Users-screen filter coordinator.
- Removed the **Last Login** column and Last Login filtering from the WordPress Users screen.
- Consolidated customer filter controls under a single shared **Filter** button.

### Internal Improvements

- Added dedicated services for purchase tracking, Users table purchase columns, and purchase filtering.
- Reused relative-time formatting across activity and purchase information.
- Separated shared Users-screen filter presentation from activity- and purchase-specific filtering logic.
- Added WooCommerce and WordPress test doubles for order data, price formatting, order statuses, and related functions.
- Kept purchase tracking, presentation, filtering, and shared admin UI responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for the complete user purchase tracking feature.
- Added regression coverage for older orders overwriting newer purchase metadata.
- Added coverage for purchase filter selection persistence and combined user meta queries.
- Added coverage for the shared Users-screen filter toolbar and single Filter button.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.2.0] - 2026-08-13

### Added

- Added customer login and activity tracking for WordPress users.
- Added **Last Login** and **Last Activity** timestamps to user metadata.
- Added throttled activity tracking for logged-in users across the frontend and WordPress administration area.
- Added **Last Login** and **Last Activity** columns to the WordPress Users screen.
- Added relative-time formatting for customer activity timestamps.
- Added sortable **Last Login** and **Last Activity** columns.
- Added activity filters for:
  - Logged in or active within 1 day
  - Logged in or active within 7 days
  - Logged in or active within 30 days
  - Never logged in
  - Never active
- Added defensive handling of missing, empty, and zero-valued activity timestamps.
- Added PHPUnit coverage for:
  - User login and activity tracking
  - Activity update throttling
  - Activity timestamp formatting
  - Users table activity columns
  - Activity column sorting
  - Last Login and Last Activity filters
  - Combined activity filters
  - Existing user meta query preservation
  - Invalid filter values
  - Legacy and missing activity metadata

### Changed

- Extended the WordPress Users screen with customer activity information and filtering controls.
- Positioned activity filters separately from WordPress's built-in user role controls.
- Limited activity filter controls to the top Users toolbar to prevent duplicate request parameters.

### Internal Improvements

- Added dedicated services for activity tracking, timestamp formatting, Users table columns, and activity filtering.
- Added deterministic namespaced `time()` test support for timestamp-dependent behavior.
- Added WordPress test doubles for user metadata, user queries, admin controls, and related functions.
- Kept activity tracking, presentation, and query modification responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for the complete user activity tracking feature.
- Added regression coverage for duplicate Users-screen filter fields and filter selection persistence.
- Added coverage for defensive handling of legacy activity metadata.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.1.1] - 2026-08-12

### Changed

- Adapted plugin to `shurloc-tools` new namespace, `Shurloc\Tools`.

## [0.1.0] - 2026-08-12

### Added

- Namespaced plugin at `Shurloc\CustomerTools`.
- Added `constants.php` and `bootstrap.php`.
- Added `Shurloc_Autoloader` to autoload classes.
- Added dedicated **Customers** submenu to **Shurloc Tools** top-level menu.

### Testing

- Set up PHPCS and Composer script `lint`.
- Set up PHPStan and Composer script `check`.
- Set up PHPUnit and Composer script `test`.
